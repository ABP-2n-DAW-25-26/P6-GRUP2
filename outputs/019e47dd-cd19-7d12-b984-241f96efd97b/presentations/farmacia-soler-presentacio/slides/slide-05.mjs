import { jsx } from '@oai/artifact-tool/presentation-jsx/jsx-runtime';

import {
  bottomNote,
  colors,
  fills,
  heading,
  label,
  statCard,
  subtitleText,
} from './theme.mjs';

export async function slide05(presentation) {
  const slide = presentation.slides.add();
  slide.background.fill = fills.dark;

  slide.compose(
    jsx('panel', {
      width: 'fill',
      height: 'fill',
      padding: 52,
      children: jsx('column', {
        width: 'fill',
        height: 'fill',
        justify: 'between',
        gap: 28,
        children: [
          jsx('column', {
            width: 900,
            gap: 12,
            children: [
              label('4 · VALOR I DIFERENCIAL', '#FFFFFF1A', colors.white),
              heading('Que aporta aquesta plataforma', colors.white, 34),
              subtitleText('Mes que una web: una eina de treball i atencio al client per a un negoci real.', '#D3DEE7'),
            ],
          }),
          jsx('row', {
            width: 'fill',
            height: 310,
            gap: 18,
            children: [
              statCard('A', 'Centralitzacio', 'Reuneix en una sola plataforma processos que abans estaven dispersos.', {
                fill: '#FFFFFF',
                numberColor: colors.brandDark,
              }),
              statCard('B', 'Comoditat', 'Fa mes facil reservar, inscriure’s o consultar informacio des de la part publica.', {
                fill: '#EDF4F8',
                numberColor: colors.navy,
              }),
              statCard('C', 'Automatitzacio', 'Activa correus, confirmacions i fluxos que redueixen gestio manual.', {
                fill: '#FFF5E8',
                numberColor: colors.gold,
              }),
              statCard('D', 'Control', 'Dona visibilitat i administracio sobre serveis, tallers, usuaris i comunicacions.', {
                fill: '#E7F3EE',
                numberColor: colors.brand,
              }),
            ],
          }),
          jsx('panel', {
            fill: '#FFFFFF12',
            borderRadius: 24,
            padding: 22,
            children: bottomNote(
              'No es nomes una practica academica: es una solucio realista per digitalitzar processos reals de la Farmacia Soler.',
              colors.white,
            ),
          }),
        ],
      }),
    }),
  );

  return slide;
}
