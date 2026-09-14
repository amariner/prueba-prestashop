import sharp from "sharp";
import { fileURLToPath } from "node:url";
import { readFile, mkdir, writeFile } from "node:fs/promises";
const catalog = JSON.parse(
  await readFile(new URL("../catalog/products.json", import.meta.url)),
);
const imgDir = new URL("../themes/brisa/assets/img/", import.meta.url);
await mkdir(imgDir, { recursive: true });
const esc = (s) => s.replaceAll("&", "&amp;").replaceAll("<", "&lt;");
const defs = `<defs><filter id="shadow" x="-100%" y="-60%" width="300%" height="250%"><feDropShadow dx="13" dy="17" stdDeviation="15" flood-color="#273b25" flood-opacity=".17"/></filter><filter id="blur"><feGaussianBlur stdDeviation="20"/></filter><linearGradient id="glass" x1="0" x2="1"><stop stop-color="#fff" stop-opacity=".3"/><stop offset=".15" stop-color="#fff" stop-opacity=".09"/><stop offset=".35" stop-color="#fff" stop-opacity=".24"/><stop offset=".62" stop-color="#fff" stop-opacity="0"/><stop offset="1" stop-color="#16261d" stop-opacity=".2"/></linearGradient><linearGradient id="cap" x1="0" x2="1"><stop stop-color="#263d31"/><stop offset=".4" stop-color="#4b5c42"/><stop offset="1" stop-color="#1e3027"/></linearGradient><linearGradient id="wall" x1="0" x2="1" y2="1"><stop stop-color="#f1f0df"/><stop offset="1" stop-color="#d4ddc8"/></linearGradient><linearGradient id="pedestal" x1="0" x2="1"><stop stop-color="#e9ddc8"/><stop offset=".7" stop-color="#d0c1a8"/><stop offset="1" stop-color="#bfad95"/></linearGradient></defs>`;
function bottle(p, x, y, scale = 1, rotation = 0, id = 0) {
  let top = "",
    body = "";
  if (p.shape === "spray") {
    top = `<path d="M85 56h64V18h77V5h-106q-27 0-35 28z" fill="url(#cap)"/><path d="M155 23q32 7 38 34l-16 5q-9-21-28-22z" fill="#2d4233"/><rect x="88" y="49" width="59" height="30" rx="4" fill="url(#cap)"/>`;
    body = `<path d="M86 72h65v23q0 18 22 35 25 17 25 48v229q0 23-25 23H64q-25 0-25-23V177q0-33 24-49 23-16 23-33z"/>`;
  } else if (p.shape === "pump") {
    top = `<rect x="98" y="13" width="38" height="52" rx="3" fill="#3c4c35"/><path d="M94 16V3h93q16 0 16 16v9h-19V18z" fill="url(#cap)"/><rect x="80" y="54" width="74" height="34" rx="6" fill="url(#cap)"/>`;
    body = `<path d="M79 80h76v25q0 10 16 20 24 17 24 49v231q0 24-24 24H65q-25 0-25-24V174q0-32 22-49 17-12 17-20z"/>`;
  } else {
    top = `<rect x="51" y="33" width="67" height="38" rx="7" fill="url(#cap)"/><path d="M58 39v25m8-25v25m8-25v25m8-25v25m8-25v25m8-25v25m8-25v25" stroke="#788268" stroke-opacity=".45" stroke-width="2"/>`;
    body = `<path fill-rule="evenodd" d="M52 66h66v30h37q54 0 57 62l11 237q0 35-33 35H38q-28 0-27-32l6-245q0-31 35-50zm107 62q-24-1-23 29l2 53q1 19 20 19 26 0 25-20l-3-53q-1-28-21-28z"/>`;
  }
  const words = p.name.split(" "),
    name1 = esc(words.shift()),
    name2 = esc(words.join(" "));
  return `<g transform="translate(${x} ${y}) rotate(${rotation} 118 215) scale(${scale})"><ellipse cx="133" cy="433" rx="100" ry="14" fill="#3d462a" opacity=".13" filter="url(#blur)"/><g filter="url(#shadow)">${top}<g fill="${p.color}">${body}</g><g fill="url(#glass)">${body}</g><rect x="${p.shape === "jug" ? 28 : 50}" y="207" width="${p.shape === "jug" ? 179 : 137}" height="161" rx="2" fill="#f7f4e7"/><path d="M63 268h109" stroke="#b1b8a1" stroke-width=".8"/><text x="117" y="253" text-anchor="middle" fill="#254b3d" font-family="sans-serif" font-size="40" font-weight="700" letter-spacing="-2">brisa<tspan font-size="9" dy="-21">®</tspan></text><text x="117" y="289" text-anchor="middle" fill="#3c5140" font-family="sans-serif" font-size="12">${name1}</text><text x="117" y="307" text-anchor="middle" fill="#3c5140" font-family="sans-serif" font-size="12">${name2}</text><text x="117" y="340" text-anchor="middle" fill="#748064" font-family="sans-serif" font-size="7" letter-spacing="1.8">CUIDADO DEL HOGAR</text><text x="117" y="356" text-anchor="middle" fill="#748064" font-family="sans-serif" font-size="7">${p.shape === "jug" ? "1 L" : "500 ml"} · DEMO</text></g></g>`;
}
function wrap(width, height, content) {
  return `<svg xmlns="http://www.w3.org/2000/svg" width="${width}" height="${height}" viewBox="0 0 ${width} ${height}">${defs}${content}</svg>`;
}
function leaf(x, y, scale = 1) {
  return `<g transform="translate(${x} ${y}) scale(${scale})" opacity=".8"><path d="M0 170Q30 95 100 0" fill="none" stroke="#76846a" stroke-width="4"/><path d="M30 115Q-30 48 21 65 42 71 30 115M44 90Q103 62 82 34 67 30 44 90M66 53Q38 3 67 6 85 13 66 53M16 140Q84 105 57 104 38 103 16 140" fill="#7f9273"/></g>`;
}
for (const [i, p] of catalog.products.entries()) {
  const svg = wrap(
    800,
    800,
    `<rect width="800" height="800" fill="${p.background}"/><ellipse cx="430" cy="688" rx="145" ry="26" fill="#5e6a47" opacity=".1" filter="url(#blur)"/>${bottle(p, 250, 155, 1.32, -5 + (i % 3) * 4, i)}`,
  );
  await sharp(Buffer.from(svg))
    .jpeg({ quality: 92 })
    .toFile(
      fileURLToPath(
        new URL(`../catalog/images/${p.slug}.jpg`, import.meta.url),
      ),
    );
}
const hero = wrap(
  1200,
  1100,
  `<rect width="1200" height="1100" fill="url(#wall)"/><path d="M160 0h600L1200 700v190L920 810z" fill="#fffbea" opacity=".28"/><path d="M220 0h60l920 795v60zM500 0h28l672 540v35z" fill="#f9f8e7" opacity=".32"/><rect y="780" width="1200" height="320" fill="#d8d1bc"/><ellipse cx="665" cy="865" rx="410" ry="74" fill="#49503b" opacity=".17" filter="url(#blur)"/><path d="M325 770v130q275 140 640 0V770" fill="url(#pedestal)"/><ellipse cx="645" cy="770" rx="320" ry="91" fill="#e4d9c3"/>${bottle(catalog.products[2], 660, 240, 1.35, 5)}${bottle(catalog.products[0], 315, 145, 1.55, -8)}${bottle(catalog.products[1], 530, 440, 1.06, 4)}${leaf(940, 550, 1.8)}<path d="M0 1005q165-102 287-6l70 101H0" fill="#f3eee1"/><path d="M-10 1025q165-96 300 12M0 1050q148-79 306 7M0 1073q167-75 325 9" fill="none" stroke="#d4cbb9" stroke-width="3"/>`,
);
await sharp(Buffer.from(hero))
  .webp({ quality: 92 })
  .toFile(fileURLToPath(new URL("hero.webp", imgDir)));
const ritual = wrap(
  1000,
  1000,
  `<rect width="1000" height="1000" fill="#d9deca"/><path d="M0 0h600L0 730" fill="#f2eedf" opacity=".5"/><rect y="705" width="1000" height="295" fill="#c8bca4"/><path d="M170 690h475v58H170z" fill="#f2eee1"/><path d="M178 638h465v48H178z" fill="#e4dfce"/><path d="M190 595h449v40H190z" fill="#f4f0e4"/><path d="M185 614h450m-450 9h450m-449 39h461m-462 12h464m-469 40h470m-471 12h474" stroke="#d8d0bc" stroke-width="2"/>${bottle(catalog.products[7], 455, 190, 1.38, -7)}${leaf(185, 480, 2)}<ellipse cx="743" cy="835" rx="103" ry="28" fill="#f0e4cf"/><ellipse cx="743" cy="830" rx="82" ry="20" fill="#ded1b9"/>`,
);
await sharp(Buffer.from(ritual))
  .webp({ quality: 90 })
  .toFile(fileURLToPath(new URL("ritual.webp", imgDir)));
for (let i = 0; i < 4; i++) {
  const p = catalog.products[[1, 3, 2, 0][i]];
  const svg = wrap(
    600,
    640,
    `<rect width="600" height="640" fill="${p.background}"/><circle cx="470" cy="75" r="225" fill="#ffffff" opacity=".2"/><ellipse cx="300" cy="500" rx="210" ry="52" fill="#e0d5c0"/>${bottle(p, 185, 93, 0.9, -10)}${i === 0 ? '<ellipse cx="141" cy="460" rx="85" ry="29" fill="#f9f6e9"/><ellipse cx="141" cy="454" rx="63" ry="18" fill="#e0d8c5"/>' : ""}${i === 2 ? '<path d="M338 433h177v45H338z" fill="#f3eee2"/><path d="M345 394h165v39H345z" fill="#ddd9cc"/>' : ""}${leaf(420, 370, 0.7)}`,
  );
  await sharp(Buffer.from(svg))
    .webp({ quality: 90 })
    .toFile(fileURLToPath(new URL(`category-${i + 1}.webp`, imgDir)));
}
await sharp(Buffer.from(hero))
  .resize(1000, 917)
  .png()
  .toFile(
    fileURLToPath(new URL("../themes/brisa/preview.png", import.meta.url)),
  );
const favicon = wrap(
  64,
  64,
  '<rect width="64" height="64" rx="14" fill="#254b3d"/><text x="30" y="49" font-family="sans-serif" font-weight="700" font-size="52" text-anchor="middle" fill="#faf8eb">b</text>',
);
await sharp(Buffer.from(favicon))
  .png()
  .toFile(fileURLToPath(new URL("favicon.png", imgDir)));
await writeFile(new URL("hero.svg", imgDir), hero);
console.log(
  "Generated 12 product images, 6 editorial illustrations, favicon and theme preview.",
);
