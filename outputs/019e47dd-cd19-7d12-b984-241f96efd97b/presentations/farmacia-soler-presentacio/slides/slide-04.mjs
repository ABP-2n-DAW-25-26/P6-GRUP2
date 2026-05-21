import { jsx } from '@oai/artifact-tool/presentation-jsx/jsx-runtime';

import {
  bulletList,
  colors,
  heading,
  label,
  softPanel,
  subtitleText,
} from './theme.mjs';

export async function slide04(presentation) {
  const slide = presentation.slides.add();
  slide.background.fill = colors.white;

  slide.compose(
    jsx('panel', {
      width: 'fill',
      height: 'fill',
      padding: 52,
      children: jsx('row', {
        width: 'fill',
        height: 'fill',
        gap: 24,
        children: [
          jsx('column', {
            width: 640,
            height: 'fill',
            gap: 18,
            children: [
              label('3 · FUNCIONALITATS DESTACADES'),
              heading('El valor practic del projecte', colors.ink, 31),
              subtitleText('Aquestes son les funcionalitats que expliquen millor per que la plataforma es util i demo-ready.', colors.slate, 17),
              bulletList([
                'Cites: seleccio de servei, data i hora disponibles, confirmacio per correu i PDF.',
                'Tallers: inscripcio publica amb control de places.',
                'Encarrecs: codi de seguiment per consultar l’estat.',
                'Farmacies de guardia: mapa interactiu segons la data.',
                'Admin: gestio de serveis, horaris, usuaris, tallers i correus.',
              ], { size: 16 }),
              softPanel(
                jsx('text', {
                  width: 'fill',
                  style: { fontSize: 17, color: colors.white, bold: true, lineSpacing: 1.18 },
                  children: 'Aquestes funcionalitats no les explicarem totes a fons ara: les deixem preparades per veure-les millor a la demo.',
                }),
                {
                  fill: colors.brandDark,
                  padding: 22,
                },
              ),
            ],
          }),
          jsx('column', {
            width: 'fill',
            height: 'fill',
            gap: 18,
            children: [
              jsx('row', {
                width: 'fill',
                height: 310,
                gap: 18,
                children: [
                  softPanel(
                    jsx('column', {
                      width: 'fill',
                      height: 'fill',
                      justify: 'between',
                      gap: 18,
                      children: [
                        jsx('column', {
                          gap: 10,
                          children: [
                            jsx('text', {
                              style: { fontSize: 15, color: colors.brandDark, bold: true },
                              children: 'Per que aquesta slide importa',
                            }),
                            heading('Les funcionalitats son el pont entre el problema i la demo', colors.ink, 24),
                            subtitleText(
                              'No es tracta d’ensenyar totes les pantalles, sino de destacar allo que demostra valor real.',
                              colors.slate,
                              16,
                            ),
                          ],
                        }),
                        jsx('panel', {
                          fill: colors.brandDark,
                          borderRadius: 22,
                          padding: 16,
                          children: jsx('text', {
                            width: 'fill',
                            style: { fontSize: 16, color: colors.white, bold: true, lineSpacing: 1.16 },
                            children: 'Missatge clau: cada modul resol una necessitat concreta i prepara una demo facil d’entendre.',
                          }),
                        }),
                      ],
                    }),
                    { fill: '#EEF4F6', height: 'fill' },
                  ),
                  softPanel(
                    jsx('column', {
                      width: 'fill',
                      height: 'fill',
                      justify: 'between',
                      gap: 18,
                      children: [
                        jsx('text', {
                          style: { fontSize: 15, color: colors.brandDark, bold: true },
                          children: 'Flux de demo recomanat',
                        }),
                        jsx('column', {
                          gap: 16,
                          children: [
                            jsx('text', {
                              style: { fontSize: 24, color: colors.ink, bold: true },
                              children: '1. Flux public rapid',
                            }),
                            jsx('text', {
                              style: { fontSize: 18, color: colors.slate },
                              children: '2. Resultat o confirmacio',
                            }),
                            jsx('text', {
                              style: { fontSize: 18, color: colors.slate },
                              children: '3. Entrada al panell admin',
                            }),
                          ],
                        }),
                      ],
                    }),
                    { fill: colors.sand, height: 'fill' },
                  ),
                ],
              }),
              softPanel(
                jsx('row', {
                  width: 'fill',
                  height: 'fill',
                  gap: 18,
                  children: [
                    softPanel(
                      jsx('column', {
                        gap: 12,
                        children: [
                          jsx('text', {
                            style: { fontSize: 15, color: colors.brandDark, bold: true },
                            children: 'Exemple de valor public',
                          }),
                          heading('Reserva de cites', colors.ink, 24),
                          subtitleText('Usuari tria servei, data i hora disponible; rep confirmacio i PDF.', colors.slate, 16),
                        ],
                      }),
                      { fill: colors.brandSoft, height: 'fill' },
                    ),
                    softPanel(
                      jsx('column', {
                        gap: 12,
                        children: [
                          jsx('text', {
                            style: { fontSize: 15, color: colors.navy, bold: true },
                            children: 'Exemple de valor intern',
                          }),
                          heading('Panell admin', colors.ink, 24),
                          subtitleText('L’equip pot gestionar serveis, horaris, tallers, usuaris i correus des d’un sol lloc.', colors.slate, 16),
                        ],
                      }),
                      { fill: '#EEF1F8', height: 'fill' },
                    ),
                  ],
                }),
                { fill: colors.white, line: colors.line, height: 'fill' },
              ),
            ],
          }),
        ],
      }),
    }),
  );

  return slide;
}
