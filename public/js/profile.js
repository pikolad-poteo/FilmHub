(function () {
  const input = document.getElementById("avatarInput");
  const form = document.getElementById("avatarForm");
  if (!input || !form) return;

  const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');

  async function fileToSquare256(file) {
    const img = new Image();
    const url = URL.createObjectURL(file);

    await new Promise((resolve, reject) => {
      img.onload = resolve;
      img.onerror = reject;
      img.src = url;
    });

    const size = 256;
    const canvas = document.createElement("canvas");
    canvas.width = size;
    canvas.height = size;
    const ctx = canvas.getContext("2d");

    // center-crop
    const sw = img.naturalWidth;
    const sh = img.naturalHeight;
    const side = Math.min(sw, sh);
    const sx = Math.floor((sw - side) / 2);
    const sy = Math.floor((sh - side) / 2);

    ctx.drawImage(img, sx, sy, side, side, 0, 0, size, size);

    URL.revokeObjectURL(url);

    const blob = await new Promise((resolve) =>
      canvas.toBlob(resolve, "image/jpeg", 0.92)
    );

    if (!blob) throw new Error("toBlob returned null");

    return new File([blob], "avatar.jpg", { type: "image/jpeg" });
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const file = input.files && input.files[0];
    if (!file) {
      alert("Выбери изображение");
      return;
    }

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.dataset._oldText = submitBtn.textContent || "";
      if (submitBtn.textContent) submitBtn.textContent = "Загрузка...";
    }

    try {
      const resized = await fileToSquare256(file);

      const fd = new FormData();
      fd.append("avatar", resized);

      const res = await fetch(form.getAttribute("action"), {
        method: "POST",
        body: fd,
        credentials: "same-origin",
      });

      if (res.ok) {
        window.location.reload();
      } else {
        const text = await res.text().catch(() => "");
        console.error("Upload failed:", res.status, text.slice(0, 400));
        alert("Ошибка загрузки (сервер вернул " + res.status + ")");
      }
    } catch (err) {
      console.error(err);
      alert("Не удалось обработать изображение");
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        if (submitBtn.textContent) submitBtn.textContent = submitBtn.dataset._oldText || "Сохранить";
      }
    }
  });
})();
