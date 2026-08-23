// --- Interactive Folder Expansion ---
function toggleFolder(element) {
  document.querySelectorAll(".unit-folder").forEach((folder) => {
    if (folder !== element) {
      folder.classList.remove("active");
      folder.style.transform = "scale(1)"; // إرجاع الحجم الطبيعي للبطاقات الأخرى
    }
  });

  element.classList.toggle("active");
}

// --- Smooth Navigation ---
function navigateLesson(event) {
  // يمنع إغلاق القائمة عند الضغط على الدرس
  event.stopPropagation();

  // إضافة تأثير صوتي أو بصري سريع هنا (اختياري)
  let card = event.currentTarget;
  card.style.transform = "scale(0.95)";
  setTimeout(() => {
    card.style.transform = "translateX(8px)";
  }, 150);
}

// --- Smart Particle Background ---
const canvas = document.getElementById("spaceCanvas");
const ctx = canvas.getContext("2d");
let width, height, particles;

// تتبع حركة الماوس لجعل الجزيئات تتفاعل معه
let mouse = { x: null, y: null };
window.addEventListener("mousemove", (e) => {
  mouse.x = e.x;
  mouse.y = e.y;
});
window.addEventListener("mouseout", () => {
  mouse.x = null;
  mouse.y = null;
});

function initCanvas() {
  width = canvas.width = window.innerWidth;
  height = canvas.height = window.innerHeight;
  particles = [];

  const particleCount = Math.floor((width * height) / 10000); // كثافة النجوم

  for (let i = 0; i < particleCount; i++) {
    particles.push({
      x: Math.random() * width,
      y: Math.random() * height,
      radius: Math.random() * 2 + 0.5,
      vx: (Math.random() - 0.5) * 0.8,
      vy: (Math.random() - 0.5) * 0.8,
      color: Math.random() > 0.5 ? "#00f3ff" : "#bc13fe",
    });
  }
}

function animateCanvas() {
  ctx.clearRect(0, 0, width, height);

  for (let i = 0; i < particles.length; i++) {
    let p = particles[i];

    p.x += p.vx;
    p.y += p.vy;

    if (p.x < 0 || p.x > width) p.vx *= -1;
    if (p.y < 0 || p.y > height) p.vy *= -1;

    // تفاعل الماوس: ابتعاد الجزيئات قليلاً عند اقتراب الماوس
    if (mouse.x != null && mouse.y != null) {
      let dx = mouse.x - p.x;
      let dy = mouse.y - p.y;
      let distance = Math.sqrt(dx * dx + dy * dy);
      if (distance < 100) {
        p.x -= dx / 20;
        p.y -= dy / 20;
      }
    }

    ctx.beginPath();
    ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
    ctx.fillStyle = p.color;
    ctx.fill();

    for (let j = i + 1; j < particles.length; j++) {
      let p2 = particles[j];
      let dist = Math.sqrt(Math.pow(p.x - p2.x, 2) + Math.pow(p.y - p2.y, 2));

      if (dist < 100) {
        ctx.beginPath();
        ctx.strokeStyle = `rgba(0, 243, 255, ${1 - dist / 100})`;
        ctx.lineWidth = 0.5;
        ctx.moveTo(p.x, p.y);
        ctx.lineTo(p2.x, p2.y);
        ctx.stroke();
      }
    }
  }
  requestAnimationFrame(animateCanvas);
}

window.addEventListener("resize", initCanvas);
initCanvas();
animateCanvas();
