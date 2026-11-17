<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NARO RUN - Game Loncat Monokrom</title>
<style>
  :root { --fg: #222; --bg: #f7f7f7; }
  * { box-sizing: border-box; }
  html, body { margin: 0; min-height: 100%; }
  body {
    background: var(--bg);
    color: var(--fg);
    font-family: "Courier New", Courier, monospace;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 24px 16px;
    -webkit-tap-highlight-color: transparent;
  }
  header { text-align: center; margin-bottom: 20px; }
  h1 { font-size: clamp(28px, 6vw, 48px); letter-spacing: 6px; margin: 0 0 8px; }
  header p { margin: 0; font-size: 15px; }
  .game {
    width: 100%;
    max-width: 800px;
    border-top: 3px solid var(--fg);
    border-bottom: 3px solid var(--fg);
    padding: 12px 0;
    user-select: none;
    -webkit-user-select: none;
  }
  canvas { width: 100%; height: auto; display: block; touch-action: manipulation; cursor: pointer; }
  .controls {
    max-width: 800px;
    width: 100%;
    margin-top: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    justify-content: center;
    font-size: 14px;
  }
  .key {
    border: 2px solid var(--fg);
    padding: 6px 12px;
  }
  button {
    font: inherit;
    background: var(--fg);
    color: var(--bg);
    border: 2px solid var(--fg);
    padding: 8px 20px;
    cursor: pointer;
    margin-top: 20px;
  }
  button:hover { background: var(--bg); color: var(--fg); }
  footer { margin-top: 32px; font-size: 12px; opacity: .6; text-align: center; }
</style>
</head>
<body>

<header>
  <h1>NARO RUN</h1>
  <p>Lari, loncat, hindari kaktus. Seberapa jauh kamu bisa pergi?</p>
</header>

<main class="game">
  <canvas id="game" width="800" height="240"></canvas>
</main>

<div class="controls">
  <span class="key">SPASI / &uarr; / KLIK / SENTUH = LONCAT</span>
  <span class="key">&darr; = TURUN CEPAT</span>
</div>

<button id="restart" type="button">MAIN ULANG</button>

<footer>naro.airmedia.id</footer>

<script>
(function () {
  var canvas = document.getElementById('game');
  var ctx = canvas.getContext('2d');
  var W = canvas.width, H = canvas.height;
  var GROUND = 200;
  var COLOR = '#222';
  var BG = '#f7f7f7';

  // Sprite dino (16x16 piksel)
  var DINO = [
    ".........#######",
    ".........##.####",
    ".........#######",
    ".........#######",
    ".........#####..",
    "#.......#####...",
    "#......######...",
    "##....########..",
    "##...#########.#",
    "###.###########.",
    ".##############.",
    "..############..",
    "...##########...",
    "....########....",
    ".....##..##....."
  ];
  var LEGS_A = ".....##..#......";
  var LEGS_B = ".....#...##.....";
  var SCALE = 3;
  var DINO_W = 16 * SCALE;
  var DINO_H = 16 * SCALE;

  var state, dino, obstacles, clouds, speed, score, hiScore, frame, nextSpawn, last, groundBumps;

  hiScore = 0;
  try { hiScore = parseInt(localStorage.getItem('narorun_hi') || '0', 10) || 0; } catch (e) {}

  function reset() {
    dino = { x: 50, y: GROUND - DINO_H, vy: 0, onGround: true };
    obstacles = [];
    clouds = [
      { x: 150, y: 50 }, { x: 450, y: 80 }, { x: 700, y: 40 }
    ];
    groundBumps = [];
    for (var i = 0; i < 30; i++) {
      groundBumps.push({ x: Math.random() * W, w: 2 + Math.random() * 4, y: GROUND + 6 + Math.random() * 20 });
    }
    speed = 6;
    score = 0;
    frame = 0;
    nextSpawn = 80;
  }

  state = 'ready'; // ready | running | over
  reset();

  function jump() {
    if (state === 'ready') { state = 'running'; }
    if (state === 'over') { start(); return; }
    if (dino.onGround) {
      dino.vy = -12.5;
      dino.onGround = false;
    }
  }

  function start() {
    reset();
    state = 'running';
  }

  document.addEventListener('keydown', function (e) {
    if (e.code === 'Space' || e.code === 'ArrowUp') {
      e.preventDefault();
      jump();
    } else if (e.code === 'ArrowDown') {
      e.preventDefault();
      if (!dino.onGround) dino.vy += 3;
    }
  });
  canvas.addEventListener('pointerdown', function (e) {
    e.preventDefault();
    jump();
  });
  document.getElementById('restart').addEventListener('click', start);

  function spawn() {
    var count = 1 + Math.floor(Math.random() * 3);
    var h = 34 + Math.floor(Math.random() * 22);
    obstacles.push({ x: W + 10, count: count, h: h, w: count * 18 });
    var minGap = 40 + speed * 18;
    nextSpawn = minGap + Math.random() * 260;
  }

  function update(dt) {
    frame += dt;

    // dino
    dino.vy += 0.65 * dt;
    dino.y += dino.vy * dt;
    if (dino.y >= GROUND - DINO_H) {
      dino.y = GROUND - DINO_H;
      dino.vy = 0;
      dino.onGround = true;
    }

    // dunia
    speed += 0.0015 * dt;
    score += speed * dt * 0.1;

    nextSpawn -= speed * dt;
    if (nextSpawn <= 0) spawn();

    for (var i = obstacles.length - 1; i >= 0; i--) {
      obstacles[i].x -= speed * dt;
      if (obstacles[i].x + obstacles[i].w < -10) obstacles.splice(i, 1);
    }
    clouds.forEach(function (c) {
      c.x -= speed * 0.2 * dt;
      if (c.x < -60) { c.x = W + 20; c.y = 30 + Math.random() * 70; }
    });
    groundBumps.forEach(function (b) {
      b.x -= speed * dt;
      if (b.x < -10) { b.x = W + Math.random() * 40; }
    });

    // tabrakan (kotak sedikit dikecilkan supaya adil)
    var dx1 = dino.x + 10, dx2 = dino.x + DINO_W - 6;
    var dy1 = dino.y + 6, dy2 = dino.y + DINO_H - 2;
    for (var j = 0; j < obstacles.length; j++) {
      var o = obstacles[j];
      var ox1 = o.x + 3, ox2 = o.x + o.w - 3;
      var oy1 = GROUND - o.h, oy2 = GROUND;
      if (dx1 < ox2 && dx2 > ox1 && dy1 < oy2 && dy2 > oy1) {
        gameOver();
        return;
      }
    }
  }

  function gameOver() {
    state = 'over';
    var s = Math.floor(score);
    if (s > hiScore) {
      hiScore = s;
      try { localStorage.setItem('narorun_hi', String(hiScore)); } catch (e) {}
    }
  }

  // ---------- gambar ----------
  function rect(x, y, w, h) { ctx.fillRect(Math.round(x), Math.round(y), w, h); }

  function drawDino() {
    ctx.fillStyle = COLOR;
    var rows = DINO.slice();
    var legs;
    if (!dino.onGround || state !== 'running') legs = LEGS_A;
    else legs = (Math.floor(frame / 6) % 2 === 0) ? LEGS_A : LEGS_B;
    rows.push(legs);
    for (var r = 0; r < rows.length; r++) {
      for (var c = 0; c < 16; c++) {
        if (rows[r][c] === '#') {
          rect(dino.x + c * SCALE, dino.y + r * SCALE, SCALE, SCALE);
        }
      }
    }
    if (state === 'over') {
      // mata jadi X (kosongkan mata, gambar silang)
      ctx.fillStyle = BG;
      rect(dino.x + 11 * SCALE, dino.y + 1 * SCALE, SCALE, SCALE);
    }
  }

  function drawCactus(x, h) {
    ctx.fillStyle = COLOR;
    var top = GROUND - h;
    rect(x + 6, top, 6, h);               // batang
    rect(x + 7, top - 2, 4, 2);           // ujung atas
    rect(x, top + h * 0.35, 6, 4);        // sambungan kiri
    rect(x, top + h * 0.15, 4, h * 0.25); // lengan kiri
    rect(x + 12, top + h * 0.5, 6, 4);    // sambungan kanan
    rect(x + 14, top + h * 0.28, 4, h * 0.26); // lengan kanan
  }

  function drawCloud(c) {
    ctx.fillStyle = '#bbb';
    rect(c.x, c.y, 40, 6);
    rect(c.x + 8, c.y - 6, 22, 6);
    rect(c.x + 14, c.y + 6, 20, 3);
  }

  function draw() {
    ctx.fillStyle = BG;
    ctx.fillRect(0, 0, W, H);

    clouds.forEach(drawCloud);

    // tanah
    ctx.fillStyle = COLOR;
    rect(0, GROUND, W, 2);
    groundBumps.forEach(function (b) { rect(b.x, b.y, b.w, 2); });

    obstacles.forEach(function (o) {
      for (var i = 0; i < o.count; i++) drawCactus(o.x + i * 18, o.h);
    });

    drawDino();

    // skor
    ctx.fillStyle = COLOR;
    ctx.font = 'bold 18px "Courier New", monospace';
    ctx.textAlign = 'right';
    var s = String(Math.floor(score)).padStart(5, '0');
    var h = String(hiScore).padStart(5, '0');
    ctx.fillText('HI ' + h + '  ' + s, W - 16, 28);

    ctx.textAlign = 'center';
    if (state === 'ready') {
      ctx.font = 'bold 20px "Courier New", monospace';
      ctx.fillText('TEKAN SPASI ATAU KLIK UNTUK MULAI', W / 2, 110);
    } else if (state === 'over') {
      ctx.font = 'bold 32px "Courier New", monospace';
      ctx.fillText('G A M E   O V E R', W / 2, 100);
      ctx.font = '16px "Courier New", monospace';
      ctx.fillText('Tekan spasi atau klik untuk main lagi', W / 2, 130);
    }
  }

  function loop(now) {
    if (!last) last = now;
    var dt = (now - last) / 16.667;
    last = now;
    if (dt > 3) dt = 3;
    if (state === 'running') update(dt);
    draw();
    requestAnimationFrame(loop);
  }
  requestAnimationFrame(loop);
})();
</script>
</body>
</html>
