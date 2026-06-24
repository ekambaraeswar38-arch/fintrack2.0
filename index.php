<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinTrack 2.0 - Smart Financial Planning</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        canvas#space { position: fixed; top:0; left:0; width:100%; height:100%; z-index:0; cursor: default; }
        .auth-container { position: relative; z-index: 10; }
        .background-blobs { display: none; }
        body { background: #000; }
        .swallow-flash { position: fixed; border-radius: 50%; pointer-events: none; z-index: 5;
            background: radial-gradient(circle, rgba(138,43,226,0.6), transparent 70%); animation: flashPulse 0.5s ease-out forwards; }
        @keyframes flashPulse { 0%{transform:scale(0);opacity:1} 100%{transform:scale(3);opacity:0} }
        #star-counter { position: fixed; top: 20px; right: 20px; z-index: 20; color: rgba(255,255,255,0.5);
            font-family: 'Outfit'; font-size: 0.8rem; pointer-events: none; }
    </style>
</head>
<body>
    <canvas id="space"></canvas>
    <div id="star-counter">✦ Stars: <span id="scount">0</span> &nbsp; ◉ Black Holes: <span id="bhcount">0</span></div>

    <main class="auth-container">
        <div class="glass-card">
            <div class="auth-header">
                <h1 class="logo">Fin<span>Track</span> 2.0</h1>
                <p>Master your money with precision.</p>
            </div>

            <div class="auth-tabs">
                <button class="tab-btn active" onclick="switchTab('login')">Login</button>
                <button class="tab-btn" onclick="switchTab('register')">Register</button>
            </div>

            <form id="login-form" action="auth.php" method="POST" class="auth-form active">
                <input type="hidden" name="action" value="login">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-footer">
                    <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
                </div>
                <button type="submit" class="primary-btn">Sign In</button>
            </form>

            <form id="register-form" action="auth.php" method="POST" class="auth-form">
                <input type="hidden" name="action" value="register">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email Address" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="primary-btn">Create Account</button>
            </form>
        </div>
    </main>

    <script>
        // ========================
        // TAB SWITCH
        // ========================
        function switchTab(tab) {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const tabs = document.querySelectorAll('.tab-btn');
            if (tab === 'login') {
                loginForm.classList.add('active'); registerForm.classList.remove('active');
                tabs[0].classList.add('active'); tabs[1].classList.remove('active');
            } else {
                loginForm.classList.remove('active'); registerForm.classList.add('active');
                tabs[0].classList.remove('active'); tabs[1].classList.add('active');
            }
        }

        // ========================
        // SPACE CANVAS ENGINE
        // ========================
        const canvas = document.getElementById('space');
        const ctx = canvas.getContext('2d');
        let W, H;
        function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
        window.addEventListener('resize', resize);
        resize();

        // --- STARS ---
        const stars = [];
        const STAR_COUNT = 120;
        const starColors = ['#ffffff','#ffeaa7','#dfe6e9','#a29bfe','#74b9ff','#fd79a8','#55efc4'];

        function createStar() {
            return {
                x: Math.random() * W,
                y: Math.random() * H,
                radius: 1.5 + Math.random() * 3,
                color: starColors[Math.floor(Math.random() * starColors.length)],
                vx: (Math.random() - 0.5) * 0.3,
                vy: (Math.random() - 0.5) * 0.3,
                twinkle: Math.random() * Math.PI * 2,
                alive: true
            };
        }
        for (let i = 0; i < STAR_COUNT; i++) stars.push(createStar());

        // --- BLACK HOLES ---
        const blackholes = [];
        const BH_COUNT = 3;

        function createBlackhole() {
            return {
                x: 100 + Math.random() * (W - 200),
                y: 100 + Math.random() * (H - 200),
                radius: 30 + Math.random() * 20,
                pulsePhase: Math.random() * Math.PI * 2,
                particles: []
            };
        }
        for (let i = 0; i < BH_COUNT; i++) blackholes.push(createBlackhole());

        // --- DRAG STATE ---
        let dragStar = null;
        let mouseX = 0, mouseY = 0;

        canvas.addEventListener('mousedown', e => {
            const rect = canvas.getBoundingClientRect();
            mouseX = e.clientX - rect.left;
            mouseY = e.clientY - rect.top;
            // find nearest alive star
            for (let s of stars) {
                if (!s.alive) continue;
                const dx = s.x - mouseX, dy = s.y - mouseY;
                if (Math.sqrt(dx*dx + dy*dy) < s.radius + 12) {
                    dragStar = s;
                    canvas.style.cursor = 'grabbing';
                    break;
                }
            }
        });

        canvas.addEventListener('mousemove', e => {
            const rect = canvas.getBoundingClientRect();
            mouseX = e.clientX - rect.left;
            mouseY = e.clientY - rect.top;
            if (dragStar) {
                dragStar.x = mouseX;
                dragStar.y = mouseY;
            }
        });

        canvas.addEventListener('mouseup', () => {
            if (dragStar) {
                // check if near a blackhole
                for (let bh of blackholes) {
                    const dx = dragStar.x - bh.x, dy = dragStar.y - bh.y;
                    if (Math.sqrt(dx*dx + dy*dy) < bh.radius + 20) {
                        swallowStar(dragStar, bh);
                        break;
                    }
                }
                dragStar = null;
                canvas.style.cursor = 'default';
            }
        });

        // Touch support
        canvas.addEventListener('touchstart', e => {
            const t = e.touches[0];
            const rect = canvas.getBoundingClientRect();
            mouseX = t.clientX - rect.left; mouseY = t.clientY - rect.top;
            for (let s of stars) {
                if (!s.alive) continue;
                const dx = s.x - mouseX, dy = s.y - mouseY;
                if (Math.sqrt(dx*dx + dy*dy) < s.radius + 20) { dragStar = s; break; }
            }
        }, {passive: true});
        canvas.addEventListener('touchmove', e => {
            if (dragStar) {
                const t = e.touches[0];
                const rect = canvas.getBoundingClientRect();
                dragStar.x = t.clientX - rect.left;
                dragStar.y = t.clientY - rect.top;
            }
        }, {passive: true});
        canvas.addEventListener('touchend', () => {
            if (dragStar) {
                for (let bh of blackholes) {
                    const dx = dragStar.x - bh.x, dy = dragStar.y - bh.y;
                    if (Math.sqrt(dx*dx + dy*dy) < bh.radius + 20) { swallowStar(dragStar, bh); break; }
                }
                dragStar = null;
            }
        });

        // --- SWALLOW ANIMATION ---
        function swallowStar(star, bh) {
            star.alive = false;
            // spawn swallow particles
            for (let i = 0; i < 12; i++) {
                const angle = (Math.PI * 2 / 12) * i;
                bh.particles.push({
                    x: star.x, y: star.y,
                    vx: Math.cos(angle) * 2,
                    vy: Math.sin(angle) * 2,
                    life: 1.0,
                    color: star.color
                });
            }
            // DOM flash
            const flash = document.createElement('div');
            flash.className = 'swallow-flash';
            flash.style.left = (bh.x - 30) + 'px';
            flash.style.top = (bh.y - 30) + 'px';
            flash.style.width = '60px';
            flash.style.height = '60px';
            document.body.appendChild(flash);
            setTimeout(() => flash.remove(), 600);

            // respawn a new star after delay
            setTimeout(() => {
                const ns = createStar();
                // spawn at edges
                if (Math.random() > 0.5) { ns.x = Math.random() > 0.5 ? -10 : W + 10; }
                else { ns.y = Math.random() > 0.5 ? -10 : H + 10; }
                stars.push(ns);
            }, 2000);
        }

        // ========================
        // RENDER LOOP
        // ========================
        function draw() {
            ctx.clearRect(0, 0, W, H);

            // BG gradient
            const bg = ctx.createRadialGradient(W/2, H/2, 100, W/2, H/2, W);
            bg.addColorStop(0, '#0a0a1a');
            bg.addColorStop(1, '#000000');
            ctx.fillStyle = bg;
            ctx.fillRect(0, 0, W, H);

            // Draw black holes
            for (let bh of blackholes) {
                bh.pulsePhase += 0.02;
                const pulse = Math.sin(bh.pulsePhase) * 5;
                const r = bh.radius + pulse;

                // accretion disk (ring glow)
                for (let ring = 3; ring >= 0; ring--) {
                    const rr = r + ring * 12;
                    const alpha = 0.08 - ring * 0.015;
                    ctx.beginPath();
                    ctx.arc(bh.x, bh.y, rr, 0, Math.PI * 2);
                    ctx.strokeStyle = `rgba(138, 43, 226, ${alpha})`;
                    ctx.lineWidth = 2;
                    ctx.stroke();
                }

                // gravitational lensing glow
                const glow = ctx.createRadialGradient(bh.x, bh.y, r * 0.2, bh.x, bh.y, r * 1.5);
                glow.addColorStop(0, 'rgba(80, 0, 150, 0.6)');
                glow.addColorStop(0.5, 'rgba(138, 43, 226, 0.15)');
                glow.addColorStop(1, 'transparent');
                ctx.fillStyle = glow;
                ctx.beginPath();
                ctx.arc(bh.x, bh.y, r * 1.5, 0, Math.PI * 2);
                ctx.fill();

                // core (event horizon)
                const core = ctx.createRadialGradient(bh.x, bh.y, 0, bh.x, bh.y, r);
                core.addColorStop(0, '#000000');
                core.addColorStop(0.7, '#0a001a');
                core.addColorStop(1, 'rgba(60, 0, 120, 0.4)');
                ctx.fillStyle = core;
                ctx.beginPath();
                ctx.arc(bh.x, bh.y, r, 0, Math.PI * 2);
                ctx.fill();

                // update particles
                bh.particles = bh.particles.filter(p => {
                    p.x += (bh.x - p.x) * 0.08 + p.vx * 0.3;
                    p.y += (bh.y - p.y) * 0.08 + p.vy * 0.3;
                    p.life -= 0.025;
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, 2, 0, Math.PI * 2);
                    ctx.fillStyle = p.color + Math.floor(p.life * 255).toString(16).padStart(2,'0');
                    ctx.fill();
                    return p.life > 0;
                });
            }

            // Draw & move stars
            let aliveCount = 0;
            for (let s of stars) {
                if (!s.alive) continue;
                aliveCount++;

                // drift
                if (s !== dragStar) {
                    s.x += s.vx;
                    s.y += s.vy;

                    // gravitational pull towards black holes
                    for (let bh of blackholes) {
                        const dx = bh.x - s.x, dy = bh.y - s.y;
                        const dist = Math.sqrt(dx*dx + dy*dy);
                        if (dist < 200 && dist > bh.radius) {
                            const force = 0.15 / (dist * 0.05);
                            s.vx += (dx / dist) * force;
                            s.vy += (dy / dist) * force;
                        }
                        // auto-swallow if too close
                        if (dist < bh.radius * 0.5) {
                            swallowStar(s, bh);
                        }
                    }

                    // wrap edges
                    if (s.x < -20) s.x = W + 10;
                    if (s.x > W + 20) s.x = -10;
                    if (s.y < -20) s.y = H + 10;
                    if (s.y > H + 20) s.y = -10;
                }

                // twinkle
                s.twinkle += 0.05;
                const alpha = 0.5 + Math.sin(s.twinkle) * 0.4;

                // glow
                const sg = ctx.createRadialGradient(s.x, s.y, 0, s.x, s.y, s.radius * 4);
                sg.addColorStop(0, s.color);
                sg.addColorStop(1, 'transparent');
                ctx.globalAlpha = alpha * 0.3;
                ctx.fillStyle = sg;
                ctx.beginPath();
                ctx.arc(s.x, s.y, s.radius * 4, 0, Math.PI * 2);
                ctx.fill();

                // core star
                ctx.globalAlpha = alpha;
                ctx.fillStyle = s.color;
                ctx.beginPath();
                ctx.arc(s.x, s.y, s.radius, 0, Math.PI * 2);
                ctx.fill();

                // drag highlight
                if (s === dragStar) {
                    ctx.globalAlpha = 0.6;
                    ctx.strokeStyle = '#ffffff';
                    ctx.lineWidth = 1.5;
                    ctx.beginPath();
                    ctx.arc(s.x, s.y, s.radius + 8, 0, Math.PI * 2);
                    ctx.stroke();
                }

                ctx.globalAlpha = 1;
            }

            // Update counters
            document.getElementById('scount').textContent = aliveCount;
            document.getElementById('bhcount').textContent = blackholes.length;

            requestAnimationFrame(draw);
        }
        draw();
    </script>
</body>
</html>
