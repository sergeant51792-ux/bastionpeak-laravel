@props(['name' => 'circle-help', 'class' => '', 'size' => null])

@php
$svgPath = base_path('vendor/mallardduck/blade-lucide-icons/resources/svg/icons/' . $name . '.svg');
$svgContent = file_exists($svgPath) ? file_get_contents($svgPath) : file_get_contents(base_path('vendor/mallardduck/blade-lucide-icons/resources/svg/icons/circle-help.svg'));
$svgClass = trim($class . ($size ? ' w-' . $size . ' h-' . $size : ''));
if ($svgClass !== '') {
    $svgContent = preg_replace('/^<svg /', '<svg class="' . $svgClass . '" ', $svgContent, 1);
}
echo $svgContent;
@endphp
