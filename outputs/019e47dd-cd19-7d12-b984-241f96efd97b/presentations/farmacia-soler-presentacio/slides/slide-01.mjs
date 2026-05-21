import { jsx, jsxs } from '@oai/artifact-tool/presentation-jsx/jsx-runtime';

import {
  bodyText,
  colors,
  fills,
  heading,
  label,
  miniTag,
  softPanel,
  subtitleText,
} from './theme.mjs';

export async function slide01(presentation) {
  const slide = presentation.slides.add();
  slide.background.fill = fills.hero;

  slide.compose(
    jsx('panel', {
      width: 'fill',
      height: 'fill',
      padding: 52,
      children: jsx('row', {
        width: 'fill',
        height: 'fill',
        gap: 28,
        children: [
          jsx('column', {
            width: 560,
            height: 'fill',
            justify: 'between',
            gap: 28,
            children: [
              jsx('column', {
                gap: 22,
                children: [
                  label('PROJECTE 6 · PRESENTACIO', colors.white, colors.brandDark),
                  heading('Farmacia Soler\nPlataforma web de gestio i atencio al client', colors.white, 33),
                  subtitleText(
                    "Hem desenvolupat una plataforma que centralitza serveis publics i gestio interna de la farmacia.",
                    '#DCE7EE',
                    17,
                  ),
                ],
              }),
              jsx('column', {
                gap: 18,
                children: [
                  softPanel(
                    jsx('column', {
                      gap: 10,
                      children: [
                        bodyText('Client', colors.white, 16),
                        heading('Farmacia Soler (Figueres)', colors.white, 24),
                        subtitleText(
                          'Presentacio introductoria pensada per donar context abans de la demo real del producte.',
                          '#C1D2DC',
                          16,
                        ),
                      ],
                    }),
                    {
                      fill: 'rgba(255,255,255,0.12)',
                      line: 'rgba(255,255,255,0.18)',
                      padding: 22,
                    },
                  ),
                  jsx('row', {
                    gap: 10,
                    children: [
                      miniTag('Adrien Schirmer', '#FFFFFF1F', colors.white),
                      miniTag('Brandon Reyes', '#FFFFFF1F', colors.white),
                    ],
                  }),
                  jsx('row', {
                    gap: 10,
                    children: [
                      miniTag('Fran Olivera', '#FFFFFF1F', colors.white),
                      miniTag('Charfah Ilyass', '#FFFFFF1F', colors.white),
                    ],
                  }),
                ],
              }),
            ],
          }),
          jsx('panel', {
            width: 'fill',
            height: 'fill',
            borderRadius: 34,
            fill: '#FFFFFF14',
            padding: 20,
            children: jsx('column', {
              width: 'fill',
              height: 'fill',
              gap: 18,
              children: [
                jsx('panel', {
                  fill: '#FFFFFF',
                  borderRadius: 26,
                  height: 390,
                  padding: 28,
                  children: jsx('column', {
                    width: 'fill',
                    height: 'fill',
                    justify: 'between',
                    gap: 18,
                    children: [
                      jsx('column', {
                        gap: 12,
                        children: [
                          bodyText('Visio general del producte', colors.brandDark, 15),
                          heading('Una sola plataforma per a processos publics i interns', colors.ink, 30),
                          subtitleText(
                            'La presentacio introdueix el client, el problema i el valor de la solucio abans de veure el producte funcionant.',
                            colors.slate,
                            17,
                          ),
                        ],
                      }),
                      jsx('row', {
                        width: 'fill',
                        gap: 12,
                        children: [
                          softPanel(
                            jsx('column', {
                              gap: 8,
                              children: [
                                bodyText('Public', colors.brandDark, 14),
                                heading('Cites\nTallers\nEncarrecs', colors.ink, 24),
                              ],
                            }),
                            { fill: colors.brandSoft, height: 'fill' },
                          ),
                          softPanel(
                            jsx('column', {
                              gap: 8,
                              children: [
                                bodyText('Intern', colors.navy, 14),
                                heading('Admin\nHoraris\nCorreus', colors.ink, 24),
                              ],
                            }),
                            { fill: colors.sand, height: 'fill' },
                          ),
                        ],
                      }),
                    ],
                  }),
                }),
                jsx('row', {
                  width: 'fill',
                  gap: 12,
                  children: [
                    miniTag('Cites', colors.sand, colors.ink),
                    miniTag('Tallers', colors.brandSoft, colors.brandDark),
                    miniTag('Encarrecs', colors.coralSoft, colors.coral),
                    miniTag('Admin', '#FFFFFF1F', colors.white),
                  ],
                }),
                jsx('panel', {
                  fill: '#FFFFFF',
                  borderRadius: 24,
                  padding: 20,
                  children: jsxs('row', {
                    width: 'fill',
                    justify: 'between',
                    children: [
                      jsx('column', {
                        gap: 8,
                        children: [
                          bodyText('Objectiu de la presentacio', colors.slate, 14),
                          heading('Context + valor + demo', colors.ink, 24),
                        ],
                      }),
                      jsx('text', {
                        style: { fontSize: 48, color: colors.brandDark, bold: true },
                        children: '6',
                      }),
                    ],
                  }),
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
