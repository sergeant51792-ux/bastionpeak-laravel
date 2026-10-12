const { compile } = require('@tailwindcss/node');

compile('@import "tailwindcss";', {
  base: process.cwd(),
  onDependency: () => {},
})
  .then((r) => {
    console.log('compile OK');
    const css = r.build(['flex', 'text-red-500']);
    console.log('css length', css.length);
  })
  .catch((e) => console.log('ERR', e && e.message));
