import { jsx } from '@oai/artifact-tool/presentation-jsx/jsx-runtime';
import { stroke } from '@oai/artifact-tool/presentation-jsx';

import {
  bodyText,
  bulletList,
  colors,
  fills,
  heading,
  label,
  softPanel,
  subtitleText,
} from './theme.mjs';

export async function slide02(presentation) {
  const slide = presentation.slides.add();
  slide.background.fill = fills.warm;

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
          jsx('row', {
            width: 'fill',
            justify: 'between',
            children: [
              jsx('column', {
                width: 760,
                gap: 12,
                children: [
                  label('1 · NECESSITAT DEL CLIENT'),
                  heading('Qui es el client i quin problema te', colors.ink, 33),
                  subtitleText(
                    'Abans de parlar de funcionalitats, situem el negoci i la necessitat real que voliem resoldre.',
                  ),
                ],
              }),
              softPanel(
                jsx('text', {
                  width: 'hug',
                  style: { fontSize: 18, color: colors.brandDark, bold: true },
                  children: 'Abans / Despres',
                }),
                {
                  fill: colors.white,
                  line: colors.line,
                  width: 'hug',
                  padding: { top: 16, right: 20, bottom: 16, left: 20 },
                },
              ),
            ],
          }),
          jsx('row', {
            width: 'fill',
            height: 'fill',
            gap: 20,
            children: [
              softPanel(
                jsx('column', {
                  width: 'fill',
                  height: 'fill',
                  gap: 18,
                  children: [
                    bodyText('Abans', colors.coral, 15),
                    heading('Procesos dispersos i molta gestio manual', colors.ink, 26),
                    bulletList(
                      [
                        'Gestio separada de cites, tallers, encarregs i comunicacions.',
                        "Mes dependecia de tasques manuals i menys visibilitat global.",
                        "Mes friccio tant per al client com per a l'equip intern.",
                      ],
                      { accent: colors.coral, textColor: colors.ink, size: 18 },
                    ),
                  ],
                }),
                {
                  fill: colors.white,
                  line: colors.line,
                  height: 'fill',
                },
              ),
              jsx('panel', {
                width: 70,
                height: 'fill',
                borderRadius: 35,
                fill: colors.brandDark,
                children: jsx('column', {
                  width: 'fill',
                  height: 'fill',
                  justify: 'between',
                  align: 'center',
                  padding: 18,
                  children: [
                    jsx('shape', {
                      geometry: 'ellipse',
                      width: 14,
                      height: 14,
                      fill: colors.white,
                    }),
                    jsx('rule', {
                      width: 2,
                      height: 'fill',
                      stroke: '#FFFFFF55',
                      weight: 2,
                    }),
                    jsx('shape', {
                      geometry: 'ellipse',
                      width: 14,
                      height: 14,
                      fill: colors.sand,
                    }),
                  ],
                }),
              }),
              softPanel(
                jsx('column', {
                  width: 'fill',
                  height: 'fill',
                  gap: 18,
                  children: [
                    bodyText('Despres', colors.brand, 15),
                    heading('Una eina unica per ordenar i digitalitzar', colors.ink, 26),
                    bulletList(
                      [
                        "Centralitzacio dels fluxos publics i de l'operativa interna.",
                        "Mes comoditat per a l'usuari i mes control per a l'administracio.",
                        'Digitalitzacio d’un negoci real amb processos reals.',
                      ],
                      { accent: colors.brand, textColor: colors.ink, size: 18 },
                    ),
                  ],
                }),
                {
                  fill: colors.brandSoft,
                  line: colors.brand,
                  height: 'fill',
                },
              ),
            ],
          }),
          jsx('panel', {
            fill: '#FFFFFFB8',
            borderRadius: 22,
            padding: 18,
            children: jsx('text', {
              style: { fontSize: 17, color: colors.ink },
              children: 'La idea no era fer una web informativa, sino convertir processos reals en una experiencia digital mes ordenada.',
            }),
          }),
        ],
      }),
    }),
  );

  return slide;
}
