import { jsx } from '@oai/artifact-tool/presentation-jsx/jsx-runtime';

import {
  bodyText,
  bulletList,
  colors,
  heading,
  label,
  softPanel,
  subtitleText,
} from './theme.mjs';

export async function slide06(presentation) {
  const slide = presentation.slides.add();
  slide.background.fill = '#F3F7F9';

  slide.compose(
    jsx('panel', {
      width: 'fill',
      height: 'fill',
      padding: 52,
      children: jsx('column', {
        width: 'fill',
        height: 'fill',
        justify: 'between',
        gap: 26,
        children: [
          jsx('column', {
            width: 900,
            gap: 12,
            children: [
              label('5 · BASE TECNICA I FIABILITAT'),
              heading('Tecnologia i qualitat', colors.ink, 34),
              subtitleText('Una base moderna, funcional i robusta, explicada sense entrar encara en massa arquitectura.'),
            ],
          }),
          jsx('row', {
            width: 'fill',
            height: 360,
            gap: 18,
            children: [
              softPanel(
                jsx('column', {
                  width: 'fill',
                  height: 'fill',
                  gap: 14,
                  children: [
                    bodyText('Stack principal', colors.brandDark, 15),
                    bulletList([
                      'Laravel + Vue + Inertia per construir la plataforma.',
                      'Tailwind CSS per al sistema visual.',
                      'Fortify i 2FA per a autenticacio i seguretat.',
                      'Correus i PDF automatics per als fluxos de cites.',
                    ]),
                  ],
                }),
                {
                  fill: colors.white,
                  line: colors.line,
                  height: 'fill',
                },
              ),
              softPanel(
                jsx('column', {
                  width: 'fill',
                  height: 'fill',
                  gap: 14,
                  children: [
                    bodyText('Qualitat del producte', colors.navy, 15),
                    bulletList([
                      'Tests amb Pest per validar funcionalitats clau.',
                      'Tests E2E amb Playwright per reforcar fluxos reals.',
                      'Arquitectura separada entre rutes, validacio, controladors i logica.',
                      'Panell admin i fluxos publics dins d’una sola experiencia coherent.',
                    ], {
                      accent: colors.navy,
                    }),
                  ],
                }),
                {
                  fill: '#EAF1F5',
                  line: '#C8D5DE',
                  height: 'fill',
                },
              ),
            ],
          }),
          jsx('panel', {
            fill: colors.brandDark,
            borderRadius: 24,
            padding: 20,
            children: jsx('column', {
              gap: 8,
              children: [
                jsx('text', {
                  style: { fontSize: 15, color: '#BFD6CE', bold: true },
                  children: 'Tancament abans de la demo',
                }),
                jsx('text', {
                  width: 'fill',
                  style: { fontSize: 21, color: colors.white, bold: true, lineSpacing: 1.14 },
                  children: 'Un cop vista la idea general del projecte, ara us ensenyarem una demo real per veure com funciona en practica.',
                }),
              ],
            }),
          }),
        ],
      }),
    }),
  );

  return slide;
}
