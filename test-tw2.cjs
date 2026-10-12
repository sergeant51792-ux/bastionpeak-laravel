const { compile, optimize } = require('@tailwindcss/node');

compile('@import "tailwindcss";', {
  base: process.cwd(),
  onDependency: () => {},
})
  .then((r) => {
    const css = r.build(['flex', 'text-red-500']);
    console.log('build OK, css length', css.length);
    const min = optimize(css, { minify: true });
    console.log('optimize OK, length', min.code ? min.code.length : JSON.stringify(min).slice(0, 80));
  })
  .catch((e) => console.log('ERR', e && e.stack));
