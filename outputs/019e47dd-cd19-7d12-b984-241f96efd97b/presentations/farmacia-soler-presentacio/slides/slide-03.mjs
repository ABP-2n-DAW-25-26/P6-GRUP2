import { jsx } from '@oai/artifact-tool/presentation-jsx/jsx-runtime';

import {
  bodyText,
  colors,
  fills,
  heading,
  label,
  statCard,
  subtitleText,
} from './theme.mjs';

export async function slide03(presentation) {
  const slide = presentation.slides.add();
  slide.background.fill = fills.mint;

  slide.compose(
    jsx('panel', {
      width: 'fill',
      height: 'fill',
      padding: 52,
      children: jsx('column', {
        width: 'fill',
        height: 'fill',
        gap: 28,
        children: [
          jsx('column', {
            width: 860,
            gap: 12,
            children: [
              label('2 · LA NOSTRA SOLUCIO'),
              heading('Que hem construit', colors.ink, 34),
              subtitleText('La plataforma cobreix tant la part publica per al client com la part interna de gestio.'),
            ],
          }),
          jsx('row', {
            width: 'fill',
            height: 250,
            gap: 18,
            children: [
              statCard('01', 'Reserva de cites', 'Seleccio de servei, data i hora disponibles dins dels horaris configurats.', {
                fill: colors.white,
              }),
              statCard('02', 'Tallers', 'Cataleg public amb inscripcio i control de places per a cada activitat.', {
                fill: '#F3F7F9',
              }),
              statCard('03', 'Encarrecs a domicili', 'Solicitut publica amb codi de seguiment per consultar l’estat.', {
                fill: '#FFF7EC',
                numberColor: colors.gold,
              }),
            ],
          }),
          jsx('row', {
            width: 'fill',
            height: 'fill',
            gap: 18,
            children: [
              statCard('04', 'Farmacies de guardia', 'Mapa interactiu per mostrar les farmacies obertes segons la data.', {
                fill: '#F8FBFD',
              }),
              jsx('panel', {
                fill: colors.brandDark,
                borderRadius: 28,
                width: 'fill',
                height: 'fill',
                padding: 24,
                children: jsx('column', {
                  width: 'fill',
                  height: 'fill',
                  justify: 'between',
                  gap: 18,
                  children: [
                    bodyText('05', '#B9D8CF', 15),
                    heading('Panell d’administracio', colors.white, 27),
                    subtitleText(
                      'Gestio de serveis, horaris, usuaris, tallers, correus i la resta de processos interns.',
                      '#D4E4EA',
                      17,
                    ),
                  ],
                }),
              }),
            ],
          }),
        ],
      }),
    }),
  );

  return slide;
}
