import { jsx } from '@oai/artifact-tool/presentation-jsx/jsx-runtime';

export async function slide01(presentation) {
  const slide = presentation.slides.add();
  slide.compose(
    jsx('image', {
      src: '/Users/adrien/Documents/2DAW PROJECTS/P6-GRUP2-ReFork/storage/app/public/farmaciaSolerStore.webp',
      width: 300,
      height: 200,
      fit: 'cover',
    }),
  );
  return slide;
}
