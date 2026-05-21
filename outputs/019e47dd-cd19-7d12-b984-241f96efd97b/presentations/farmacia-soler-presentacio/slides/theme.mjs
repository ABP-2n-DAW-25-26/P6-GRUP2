import { jsx, jsxs } from '@oai/artifact-tool/presentation-jsx/jsx-runtime';
import { paint, stroke } from '@oai/artifact-tool/presentation-jsx';

export const colors = {
  ink: '#12202B',
  slate: '#5B6B77',
  line: '#D9E2E8',
  mist: '#F4F7F8',
  white: '#FFFFFF',
  brand: '#2F7D6B',
  brandDark: '#1C5B4B',
  brandSoft: '#DDEEE8',
  sand: '#F2E8DA',
  coral: '#CC6B5A',
  coralSoft: '#F6E2DD',
  gold: '#C3922E',
  navy: '#163B57',
};

export const fonts = {
  display: 'Aptos Display',
  body: 'Aptos',
};

export const assets = {
  logo: '/Users/adrien/Documents/2DAW PROJECTS/P6-GRUP2-ReFork/storage/app/public/FarmaciaSolerLogoLong.webp',
  store: '/Users/adrien/Documents/2DAW PROJECTS/P6-GRUP2-ReFork/storage/app/public/farmaciaSolerStore.webp',
  hero: '/Users/adrien/Documents/2DAW PROJECTS/P6-GRUP2-ReFork/storage/app/public/vagaro-ckWrZO5sWOQ-unsplash.webp',
  workshop: '/Users/adrien/Documents/2DAW PROJECTS/P6-GRUP2-ReFork/storage/app/public/photos/workshops/J4n97bPH91AqzCMzsbc5g7cZriJDF7gzUjoBVVC0.png',
};

export function deckBackground(fill = colors.mist) {
  return jsx('panel', {
    fill,
    width: 'fill',
    height: 'fill',
  });
}

export function page({
  background = colors.mist,
  padding = 52,
  gap = 28,
  children,
}) {
  return jsxs('stack', {
    width: 'fill',
    height: 'fill',
    children: [
      deckBackground(background),
      jsx('panel', {
        width: 'fill',
        height: 'fill',
        padding,
        children: jsx('column', {
          width: 'fill',
          height: 'fill',
          gap,
          children,
        }),
      }),
    ],
  });
}

export function titleBlock(kicker, title, subtitle) {
  return jsx('column', {
    width: 'fill',
    gap: 12,
    children: [
      label(kicker),
      heading(title),
      subtitleText(subtitle),
    ],
  });
}

export function label(value, fill = colors.brandSoft, textColor = colors.brandDark) {
  return jsx('panel', {
    fill,
    borderRadius: 999,
    width: 'hug',
    padding: { top: 8, right: 14, bottom: 8, left: 14 },
    children: jsx('text', {
      width: 'hug',
      style: {
        fontSize: 14,
        color: textColor,
        bold: true,
        typeface: fonts.body,
      },
      children: value,
    }),
  });
}

export function heading(value, color = colors.ink, size = 34) {
  return jsx('text', {
    width: 'fill',
    style: {
      fontSize: size,
      color,
      bold: true,
      typeface: fonts.display,
      lineSpacing: 1.05,
    },
    children: value,
  });
}

export function subtitleText(value, color = colors.slate, size = 18) {
  return jsx('text', {
    width: 'fill',
    style: {
      fontSize: size,
      color,
      typeface: fonts.body,
      lineSpacing: 1.28,
    },
    children: value,
  });
}

export function bodyText(value, color = colors.ink, size = 18) {
  return jsx('text', {
    width: 'fill',
    style: {
      fontSize: size,
      color,
      typeface: fonts.body,
      lineSpacing: 1.25,
    },
    children: value,
  });
}

export function bulletList(items, options = {}) {
  const accent = options.accent ?? colors.brand;
  const textColor = options.textColor ?? colors.ink;
  const size = options.size ?? 18;

  return jsx('column', {
    width: 'fill',
    gap: 12,
    children: items.map((item) =>
      jsx('row', {
        width: 'fill',
        align: 'start',
        gap: 12,
        children: [
          jsx('shape', {
            geometry: 'ellipse',
            width: 8,
            height: 8,
            fill: accent,
          }),
          jsx('text', {
            width: 'fill',
            style: {
              fontSize: size,
              color: textColor,
              typeface: fonts.body,
              lineSpacing: 1.25,
            },
            children: item,
          }),
        ],
      }),
    ),
  });
}

export function quotePanel(text, fill = colors.brandDark, textColor = colors.white) {
  return jsx('panel', {
    fill,
    borderRadius: 28,
    padding: 28,
    children: jsx('text', {
      width: 'fill',
      style: {
        fontSize: 22,
        color: textColor,
        bold: true,
        typeface: fonts.display,
        lineSpacing: 1.2,
      },
      children: text,
    }),
  });
}

export function statCard(number, title, body, options = {}) {
  return jsx('panel', {
    fill: options.fill ?? colors.white,
    line: stroke(`1 ${options.line ?? colors.line}`),
    borderRadius: options.radius ?? 26,
    width: 'fill',
    height: 'fill',
    padding: 24,
    children: jsx('column', {
      width: 'fill',
      height: 'fill',
      justify: 'between',
      gap: 18,
        children: [
          jsx('text', {
          style: {
            fontSize: 15,
            color: options.numberColor ?? colors.brand,
            bold: true,
            typeface: fonts.body,
          },
          children: number,
        }),
        jsx('column', {
          width: 'fill',
          gap: 10,
          children: [
            jsx('text', {
              width: 'fill',
              style: {
                fontSize: 23,
                color: options.titleColor ?? colors.ink,
                bold: true,
                typeface: fonts.display,
                lineSpacing: 1.15,
              },
              children: title,
            }),
            jsx('text', {
              width: 'fill',
              style: {
                fontSize: 16,
                color: options.bodyColor ?? colors.slate,
                typeface: fonts.body,
                lineSpacing: 1.28,
              },
              children: body,
            }),
          ],
        }),
      ],
    }),
  });
}

export function imageCard(source, options = {}) {
  return jsx('panel', {
    fill: options.fill ?? colors.white,
    borderRadius: options.radius ?? 32,
    padding: options.outerPadding ?? 0,
    width: options.width ?? 'fill',
    height: options.height ?? 'fill',
    children: jsx('image', {
      src: source,
      width: 'fill',
      height: 'fill',
      fit: options.fit ?? 'cover',
      borderRadius: options.radius ?? 32,
    }),
  });
}

export function divider(color = colors.line) {
  return jsx('rule', {
    width: 'fill',
    stroke: color,
    weight: 1,
  });
}

export function softPanel(children, options = {}) {
  return jsx('panel', {
    fill: options.fill ?? colors.white,
    line: options.line ? stroke(`1 ${options.line}`) : undefined,
    borderRadius: options.radius ?? 28,
    padding: options.padding ?? 24,
    width: options.width ?? 'fill',
    height: options.height ?? 'hug',
    children,
  });
}

export function miniTag(text, fill = colors.white, color = colors.brandDark) {
  return jsx('panel', {
    fill,
    borderRadius: 999,
    width: 'hug',
    padding: { top: 8, right: 14, bottom: 8, left: 14 },
    children: jsx('text', {
      width: 'hug',
      style: {
        fontSize: 13,
        color,
        bold: true,
        typeface: fonts.body,
      },
      children: text,
    }),
  });
}

export function bottomNote(value, color = colors.slate) {
  return jsx('text', {
    width: 'fill',
    style: {
      fontSize: 15,
      color,
      bold: true,
      typeface: fonts.body,
      lineSpacing: 1.22,
    },
    children: value,
  });
}

export const fills = {
  hero: paint(`linear(135deg, ${colors.brandDark} 0%, ${colors.navy} 100%)`),
  warm: paint(`linear(135deg, ${colors.sand} 0%, ${colors.white} 75%)`),
  mint: paint(`linear(135deg, ${colors.brandSoft} 0%, ${colors.white} 100%)`),
  dark: paint(`linear(135deg, ${colors.ink} 0%, ${colors.navy} 100%)`),
};
