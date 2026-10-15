<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class TransferBlockerTest extends TestCase
{
    protected function admin(): User
    {
        $admin = User::role('Super Admin')->first();
        $this->assertNotNull($admin, 'No super admin user found for testing');

        return $admin;
    }

    protected function customer(): User
    {
        $customer = User::create([
            'name' => 'Transfer Blocker Test Customer',
            'email' => 'transfer-blocker-test@example.com',
            'password' => bcrypt('CustomerPass1!'),
            'status' => 'active',
        ]);
        $customer->assignRole('Customer');

        return $customer;
    }

    public function test_customer_cannot_access_transfer_blocker_page(): void
    {
        $customer = $this->customer();
        Auth::login($customer);

        $this->get('/admin/transfer-blocker')->assertStatus(403);

        Auth::logout();
    }

    public function test_super_admin_can_access_transfer_blocker_page(): void
    {
        Auth::login($this->admin());

        $this->get('/admin/transfer-blocker')->assertStatus(200);

        Auth::logout();
    }

    public function test_admin_can_block_transfers_with_custom_message(): void
    {
        $admin = $this->admin();
        $customer = $this->customer();

        Auth::login($admin);

        $response = $this->post(route('admin.transfer-blocker.block', $customer), [
            'reason' => 'Outgoing transfers are locked until the requested wire fee is received.',
        ]);

        $response->assertRedirect();

        $customer->refresh();
        $this->assertTrue((bool) $customer->transfer_blocked);
        $this->assertSame('Outgoing transfers are locked until the requested wire fee is received.', $customer->transfer_block_reason);
        $this->assertNotNull($customer->transfer_blocked_at);
        $this->assertSame($admin->id, (int) $customer->transfer_blocked_by);
        $this->assertTrue($customer->isTransferBlocked());

        $this->assertDatabaseHas('audit_logs', [
            'admin_id' => $admin->id,
            'action' => 'transfer_block',
            'target_type' => 'user',
            'target_id' => $customer->id,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'type' => 'security_alert',
            'title' => 'Transfers locked',
            'body' => 'Outgoing transfers are locked until the requested wire fee is received.',
        ]);

        Auth::logout();
    }

    public function test_blocking_transfers_requires_a_custom_message(): void
    {
        $admin = $this->admin();
        $customer = $this->customer();

        Auth::login($admin);

        $response = $this->post(route('admin.transfer-blocker.block', $customer), [
            'reason' => '',
        ]);

        $response->assertSessionHasErrors('reason');

        $customer->refresh();
        $this->assertFalse((bool) $customer->transfer_blocked);

        Auth::logout();
    }

    public function test_admin_can_unblock_transfers(): void
    {
        $admin = $this->admin();
        $customer = $this->customer();

        $customer->update([
            'transfer_blocked' => true,
            'transfer_block_reason' => 'Locked pending review.',
            'transfer_blocked_at' => now(),
            'transfer_blocked_by' => $admin->id,
        ]);

        Auth::login($admin);

        $this->post(route('admin.transfer-blocker.unblock', $customer))->assertRedirect();

        $customer->refresh();
        $this->assertFalse((bool) $customer->transfer_blocked);
        $this->assertNull($customer->transfer_block_reason);
        $this->assertNull($customer->transfer_blocked_at);
        $this->assertNull($customer->transfer_blocked_by);

        $this->assertDatabaseHas('audit_logs', [
            'admin_id' => $admin->id,
            'action' => 'transfer_unblock',
            'target_type' => 'user',
            'target_id' => $customer->id,
        ]);

        Auth::logout();
    }

    public function test_blocked_customer_sees_custom_message_on_transfer_page(): void
    {
        $customer = $this->customer();
        $customer->update([
            'transfer_blocked' => true,
            'transfer_block_reason' => 'Your transfers are locked pending compliance review.',
        ]);

        $this->actingAs($customer)
            ->get('/transfer')
            ->assertStatus(200)
            ->assertSee('Transfers locked')
            ->assertSee('Your transfers are locked pending compliance review.');
    }

    public function test_blocked_customer_cannot_submit_a_transfer_request(): void
    {
        $customer = $this->customer();
        $customer->update([
            'transfer_blocked' => true,
            'transfer_block_reason' => 'Transfers are locked until the activation fee is settled.',
        ]);

        // The blocked customer is sent back to the transfer form, where the
        // admin's message is displayed (a bare 403 page would hide it).
        $response = $this->actingAs($customer)
            ->post('/pay', [
                'payment_method' => 'wire',
                'recipient_name' => 'John Recipient',
                'amount' => 100,
                'idempotency' => bin2hex(random_bytes(32)),
                'metadata' => ['bank_name' => 'Test Bank'],
            ]);

        $response->assertRedirect(route('pay'));
        $response->assertSessionHas('transfer_blocked', true);
    }

    public function test_blocked_customer_sees_the_admin_message_on_the_transfer_form(): void
    {
        $customer = $this->customer();
        $customer->update([
            'transfer_blocked' => true,
            'transfer_block_reason' => 'Settle the pending activation fee to unlock transfers.',
        ]);

        $this->actingAs($customer)
            ->get('/pay')
            ->assertStatus(200)
            ->assertSee('Transfers locked')
            ->assertSee('Settle the pending activation fee to unlock transfers.')
            ->assertDontSee('Submit transfer request');
    }

    public function test_customer_without_a_block_can_reach_the_transfer_page(): void
    {
        $customer = $this->customer();

        $this->actingAs($customer)
            ->get('/transfer')
            ->assertStatus(200)
            ->assertDontSee('Transfers locked');
    }
}
