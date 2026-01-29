(function () {
  const input = document.getElementById("avatarInput");
  const form = document.getElementById("avatarForm");
  const preview = document.getElementById("avatarPreview");
  const fileNameEl = document.getElementById("avatarFileName");

  if (!form) return;

  // helper: ищем кнопку загрузки (именно её будем дизейблить/менять текст)
  function getUploadButton(submitter) {
    // если сабмит был кнопкой загрузки — используем её
    if (submitter && !isDeleteSubmitter(submitter)) return submitter;

    // иначе ищем кнопку без "delete" маркера
    const btns = form.querySelectorAll('button[type="submit"], input[type="submit"]');
    for (const b of btns) {
      if (!isDeleteSubmitter(b)) return b;
    }
    return null;
  }

  function isDeleteSubmitter(el) {
    if (!el) return false;
    return (
      el.dataset?.action === "delete" ||
      el.name === "delete_avatar" ||
      el.getAttribute("formaction")?.toLowerCase().includes("profileavatardelete")
    );
  }

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

  // Показ имени файла (если элемент есть)
  if (input && fileNameEl) {
    input.addEventListener("change", () => {
      const f = input.files && input.files[0];
      fileNameEl.textContent = f ? f.name : "Файл не выбран";
    });
  }

  // Превью выбранного файла (не обязательно, но приятно)
  if (input && preview) {
    input.addEventListener("change", () => {
      const f = input.files && input.files[0];
      if (!f) return;
      const url = URL.createObjectURL(f);
      preview.src = url;
      // revoke позже, чтобы картинка успела загрузиться
      preview.onload = () => URL.revokeObjectURL(url);
    });
  }

  form.addEventListener("submit", async (e) => {
    const submitter = e.submitter;

    // ✅ Если нажали "Удалить" — НЕ перехватываем, даём обычный POST на formaction
    if (isDeleteSubmitter(submitter)) {
      return;
    }

    // дальше — только логика загрузки/кропа
    e.preventDefault();

    if (!input) {
      alert("Нет поля выбора файла");
      return;
    }

    const file = input.files && input.files[0];
    if (!file) {
      alert("Выбери изображение");
      return;
    }

    const uploadBtn = getUploadButton(submitter);
    if (uploadBtn) {
      uploadBtn.disabled = true;
      uploadBtn.dataset._oldText = uploadBtn.textContent || "";
      if (uploadBtn.textContent) uploadBtn.textContent = "Загрузка...";
    }

    try {
      const resized = await fileToSquare256(file);

      const fd = new FormData();
      fd.append("avatar", resized);

      const action = form.getAttribute("action") || "";
      const res = await fetch(action, {
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
      if (uploadBtn) {
        uploadBtn.disabled = false;
        if (uploadBtn.textContent) {
          uploadBtn.textContent = uploadBtn.dataset._oldText || "Загрузить";
        }
      }
    }
  });
})();
