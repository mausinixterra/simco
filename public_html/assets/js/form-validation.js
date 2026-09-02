/**
 * Form Validation and Interaction Logic
 * Handles client-side validation, file uploads, and form interactions
 */

// Configuration constants
const CONFIG = {
  MAX_FILES: 5,
  MAX_TOTAL_SIZE: 25 * 1024 * 1024, // 25 MB
  ALLOWED_EXTENSIONS: [
    ".pdf",
    ".doc",
    ".docx",
    ".xls",
    ".xlsx",
    ".ppt",
    ".pptx",
    ".jpg",
    ".jpeg",
    ".png",
    ".gif",
  ],
};

// Validation functions
const validateEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
const validatePhone = (phone) =>
  /^[0-9\s\-()]+$/.test(phone) && phone.trim().length >= 7;

// UI Helper functions
const showError = (inputId, message) => {
  const input = document.getElementById(inputId);
  const errorElement = document.getElementById(inputId + "_error");
  if (input && errorElement) {
    input.classList.add("border-red-500", "text-red-600", "dark:text-red-500");
    input.classList.remove(
      "border-gray-300",
      "border-blue-600",
      "border-green-500",
    );
    errorElement.textContent = message;
    errorElement.classList.remove("hidden");
  }
};

const showSuccess = (inputId) => {
  const input = document.getElementById(inputId);
  const errorElement = document.getElementById(inputId + "_error");
  if (input && errorElement) {
    input.classList.remove(
      "border-red-500",
      "text-red-600",
      "dark:text-red-500",
      "border-gray-300",
    );
    input.classList.add("border-green-500");
    errorElement.classList.add("hidden");
  }
};

const clearValidation = (inputId) => {
  const input = document.getElementById(inputId);
  const errorElement = document.getElementById(inputId + "_error");
  if (input && errorElement) {
    input.classList.remove(
      "border-red-500",
      "text-red-600",
      "dark:text-red-500",
      "border-green-500",
    );
    input.classList.add("border-gray-300");
    errorElement.classList.add("hidden");
  }
};

// File handling functions
const formatFileSize = (bytes) => {
  if (bytes === 0) return "0 Bytes";
  const k = 1024;
  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i];
};

const validateFiles = (files, translations) => {
  const errorContainer = document.getElementById("file-validation-error");
  errorContainer.textContent = "";

  if (files.length > CONFIG.MAX_FILES) {
    errorContainer.textContent = `Error: ${translations.validation_max_files} ${CONFIG.MAX_FILES} ${translations.validation_files_word}.`;
    return false;
  }

  let totalSize = 0;
  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    totalSize += file.size;
    const fileExtension = "." + file.name.split(".").pop().toLowerCase();
    if (!CONFIG.ALLOWED_EXTENSIONS.includes(fileExtension)) {
      errorContainer.textContent = `Error: "${file.name}" ${translations.validation_file_type_error}`;
      return false;
    }
  }

  if (totalSize > CONFIG.MAX_TOTAL_SIZE) {
    const totalSizeInMB = (totalSize / 1024 / 1024).toFixed(2);
    errorContainer.textContent = `Error: ${translations.validation_file_size_error} (${totalSizeInMB} MB) ${translations.validation_file_size_exceeds}`;
    return false;
  }

  return true;
};

const displayFiles = (files) => {
  const fileItems = document.getElementById("file-items");
  const fileList = document.getElementById("file-list");

  fileItems.innerHTML = "";

  if (files.length === 0) {
    fileList.classList.add("hidden");
    return;
  }

  fileList.classList.remove("hidden");

  Array.from(files).forEach((file, index) => {
    const fileItem = document.createElement("div");
    // Tarjeta pequeña ajustada - estilo hoja que se adapta al espacio
    fileItem.className =
      "relative flex flex-col items-center justify-center p-2.5 bg-gradient-to-br from-white to-gray-50 dark:from-gray-700 dark:to-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-lg transition-all duration-200 flex-1 min-w-[100px] max-w-[150px] group";
    const fileExtension = "." + file.name.split(".").pop().toLowerCase();
    let fileIcon = "",
      iconColor = "",
      bgGradient = "";

    if (
      file.type.startsWith("image/") ||
      [".jpg", ".jpeg", ".png", ".gif"].includes(fileExtension)
    ) {
      iconColor = "text-blue-600 dark:text-blue-400";
      bgGradient =
        "from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30";
      fileIcon = `<svg class="w-7 h-7 ${iconColor}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>`;
    } else if ([".pdf"].includes(fileExtension)) {
      iconColor = "text-red-600 dark:text-red-400";
      bgGradient =
        "from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/30";
      fileIcon = `<svg class="w-7 h-7 ${iconColor}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg>`;
    } else if ([".doc", ".docx"].includes(fileExtension)) {
      iconColor = "text-blue-700 dark:text-blue-300";
      bgGradient =
        "from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30";
      fileIcon = `<svg class="w-7 h-7 ${iconColor}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>`;
    } else if ([".xls", ".xlsx"].includes(fileExtension)) {
      iconColor = "text-green-600 dark:text-green-400";
      bgGradient =
        "from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30";
      fileIcon = `<svg class="w-7 h-7 ${iconColor}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>`;
    } else if ([".ppt", ".pptx"].includes(fileExtension)) {
      iconColor = "text-orange-600 dark:text-orange-400";
      bgGradient =
        "from-orange-50 to-orange-100 dark:from-orange-900/30 dark:to-orange-800/30";
      fileIcon = `<svg class="w-7 h-7 ${iconColor}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg>`;
    } else {
      iconColor = "text-gray-600 dark:text-gray-400";
      bgGradient =
        "from-gray-50 to-gray-100 dark:from-gray-800/30 dark:to-gray-700/30";
      fileIcon = `<svg class="w-7 h-7 ${iconColor}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg>`;
    }

    fileItem.innerHTML = `
            <!-- Botón eliminar en esquina superior derecha -->
            <button type="button" onclick="removeFile(${index})" class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-lg transition-all opacity-0 group-hover:opacity-100 z-10">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
            
            <!-- Fondo con gradiente según tipo de archivo -->
            <div class="absolute inset-0 bg-gradient-to-br ${bgGradient} rounded-xl opacity-20"></div>
            
            <!-- Contenido de la tarjeta -->
            <div class="relative flex flex-col items-center gap-1.5 w-full">
                <!-- Icono del archivo -->
                <div class="flex-shrink-0">
                    ${fileIcon}
                </div>
                
                <!-- Nombre del archivo truncado -->
                <p class="text-[11px] font-medium text-gray-900 dark:text-gray-100 truncate w-full text-center px-1 leading-tight" title="${file.name}">
                    ${file.name.length > 14 ? file.name.substring(0, 11) + "..." : file.name}
                </p>
                
                <!-- Tamaño del archivo -->
                <span class="text-[9px] text-gray-500 dark:text-gray-400 font-medium px-1.5 py-0.5 bg-gray-200 dark:bg-gray-700 rounded-full">
                    ${formatFileSize(file.size)}
                </span>
            </div>
        `;
    fileItems.appendChild(fileItem);
  });
};

// Initialize form functionality
function initializeForm(translations) {
  // Handle form submission - show loading alert
  const form = document.getElementById("pqrsf-form");
  const submitButton = document.getElementById("submit-button");

  if (submitButton && form) {
    submitButton.addEventListener("click", function (e) {
      // Solo mostrar el alert si el formulario es válido (HTML5 validation)
      if (form.checkValidity()) {
        // Mostrar alert de carga con diseño moderno
        const loadingAlert = document.createElement("div");
        loadingAlert.id = "loading-alert";
        loadingAlert.className =
          "fixed inset-0 bg-gradient-to-br from-blue-900/90 to-gray-900/90 backdrop-blur-sm flex items-center justify-center z-50";

        loadingAlert.innerHTML = `
                    <style>
                        @keyframes progressBar {
                            0% { width: 10%; }
                            25% { width: 40%; }
                            50% { width: 65%; }
                            75% { width: 80%; }
                            100% { width: 95%; }
                        }
                        .bar-progress {
                            animation: progressBar 2.5s ease-in-out infinite;
                        }
                    </style>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 max-w-md mx-4 transform transition-all">
                        <div class="flex flex-col items-center text-center space-y-6">
                            <!-- Spinner con anillos concéntricos animados -->
                            <div class="relative w-20 h-20">
                                <!-- Anillo base estático -->
                                <div class="absolute inset-0 border-4 border-blue-200 dark:border-blue-800 rounded-full"></div>
                                <!-- Anillo exterior giratorio -->
                                <div class="absolute inset-0 border-4 border-blue-600 dark:border-blue-400 rounded-full border-t-transparent animate-spin"></div>
                                <!-- Anillo interior giratorio en reversa -->
                                <div class="absolute inset-2 border-4 border-blue-400 dark:border-blue-500 rounded-full border-b-transparent animate-spin" style="animation-duration: 1.5s; animation-direction: reverse;"></div>
                                <!-- Punto central pulsante -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-3 h-3 bg-blue-600 dark:bg-blue-400 rounded-full animate-pulse"></div>
                                </div>
                            </div>
                            
                            <!-- Título -->
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                    ${translations.loading_title}
                                </h3>
                                <p class="text-base text-gray-700 dark:text-gray-300 font-medium">
                                    ${translations.loading_message}
                                </p>
                            </div>
                            
                            <!-- Barra de progreso animada -->
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                <div class="bar-progress h-full bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-400 dark:to-blue-500 rounded-full" style="width: 10%;"></div>
                            </div>
                            
                            <!-- Mensaje de espera -->
                            <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                <svg class="w-4 h-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                ${translations.loading_wait}
                            </p>
                        </div>
                    </div>
                `;

        // Agregar el alert después de un pequeño delay para que se procese el submit
        setTimeout(() => {
          document.body.appendChild(loadingAlert);

          // Deshabilitar el botón y formulario
          submitButton.disabled = true;
          submitButton.classList.add("opacity-50", "cursor-not-allowed");

          const formElements = form.querySelectorAll(
            "input, select, textarea, button",
          );
          formElements.forEach((element) => {
            element.disabled = true;
          });
        }, 100);
      }
    });
  }

  // Choices.js for insurer dropdown
  const insurerElement = document.getElementById("aseguradora");
  if (insurerElement) {
    const choices = new Choices(insurerElement, {
      searchEnabled: true,
      itemSelectText: "Presione para seleccionar",
    });

    const applyDarkModeToChoices = () => {
      const choicesInner = document.querySelector(".choices__inner");
      const choicesDropdown = document.querySelector(
        ".choices__list--dropdown",
      );
      if (document.documentElement.classList.contains("dark")) {
        choicesInner?.classList.add(
          "!bg-gray-800",
          "!border-gray-600",
          "!text-white",
        );
        choicesInner?.classList.remove("!bg-white", "!border-gray-300");
        if (choicesDropdown) {
          choicesDropdown.classList.add("!bg-gray-800", "!border-gray-600");
          choicesDropdown.classList.remove("!bg-white");
          document
            .querySelectorAll(".choices__item--choice")
            .forEach((item) => {
              item.classList.add("!text-gray-200");
            });
        }
      } else {
        choicesInner?.classList.remove(
          "!bg-gray-800",
          "!border-gray-600",
          "!text-white",
        );
        choicesInner?.classList.add("!bg-white", "!border-gray-300");
        if (choicesDropdown) {
          choicesDropdown.classList.remove("!bg-gray-800", "!border-gray-600");
          choicesDropdown.classList.add("!bg-white");
          document
            .querySelectorAll(".choices__item--choice")
            .forEach((item) => {
              item.classList.remove("!text-gray-200", "!bg-gray-700");
            });
        }
      }
    };
    setTimeout(applyDarkModeToChoices, 100);
    const observer = new MutationObserver(applyDarkModeToChoices);
    observer.observe(document.documentElement, {
      attributes: true,
      attributeFilter: ["class"],
    });
    insurerElement.addEventListener("showDropdown", applyDarkModeToChoices);
  }

  // Handle floating labels for select elements
  document.querySelectorAll("select.peer").forEach((select) => {
    const updateSelectState = () => {
      const label = select.nextElementSibling;
      if (
        select.value &&
        select.value !== "" &&
        label &&
        label.tagName === "LABEL"
      ) {
        select.classList.add("has-value");
        label.style.transform = "translate(0, -1.5rem) scale(0.75)";
      } else {
        select.classList.remove("has-value");
        if (
          label &&
          label.tagName === "LABEL" &&
          select !== document.activeElement
        ) {
          label.style.transform = "";
        }
      }
    };
    select.addEventListener("focus", () => {
      const label = select.nextElementSibling;
      if (label && label.tagName === "LABEL") {
        label.style.transform = "translate(0, -1.5rem) scale(0.75)";
      }
    });
    select.addEventListener("blur", () => {
      if (!select.value || select.value === "") {
        const label = select.nextElementSibling;
        if (label && label.tagName === "LABEL") {
          label.style.transform = "";
        }
      }
    });
    updateSelectState();
    select.addEventListener("change", updateSelectState);
  });

  // Email and phone validation
  const setupValidation = (inputId, validator, errorMsg) => {
    const input = document.getElementById(inputId);
    if (input) {
      input.addEventListener("input", function () {
        if (this.value.trim() === "") {
          clearValidation(inputId);
        } else if (!validator(this.value)) {
          showError(inputId, errorMsg);
        } else {
          showSuccess(inputId);
        }
      });
    }
  };

  setupValidation(
    "correo_electronico",
    validateEmail,
    translations.validation_email_invalid,
  );
  setupValidation(
    "telefono_celular",
    validatePhone,
    translations.validation_phone_invalid,
  );
  setupValidation(
    "correo_electronico_peticionario",
    validateEmail,
    translations.validation_email_invalid,
  );
  setupValidation(
    "telefono_celular_peticionario",
    validatePhone,
    translations.validation_phone_invalid,
  );
  setupValidation(
    "correo_asesor",
    validateEmail,
    translations.validation_email_invalid,
  );
  setupValidation(
    "telefono_asesor",
    validatePhone,
    translations.validation_phone_invalid,
  );

  // Sync patient and petitioner data
  const fieldMappings = [
    ["nombres", "nombres_peticionario"],
    ["tipo_documento", "tipo_documento_peticionario"],
    ["documento", "documento_peticionario"],
    ["correo_electronico", "correo_electronico_peticionario"],
    ["telefono_celular", "telefono_celular_peticionario"],
  ];
  const patientFields = fieldMappings.map((m) => document.getElementById(m[0]));
  const petitionerFields = fieldMappings.map((m) =>
    document.getElementById(m[1]),
  );
  const checkbox = document.getElementById("sameAsPatient");
  const aseguradoraField = document.getElementById("aseguradora");

  const checkPatientFields = () => {
    const allFilled = patientFields.every((f) => f && f.value.trim() !== "");
    const aseguradoraFilled =
      aseguradoraField && aseguradoraField.value.trim() !== "";
    if (checkbox) checkbox.disabled = !(allFilled && aseguradoraFilled);
  };

  patientFields.forEach((f) =>
    f?.addEventListener("input", checkPatientFields),
  );

  // También escuchar cambios en el campo de aseguradora
  if (aseguradoraField) {
    aseguradoraField.addEventListener("change", checkPatientFields);
  }

  if (checkbox) {
    checkbox.addEventListener("change", () => {
      if (checkbox.checked) {
        patientFields.forEach((pf, i) => {
          if (petitionerFields[i]) {
            petitionerFields[i].value = pf.value;
            if (petitionerFields[i].tagName === "SELECT") {
              petitionerFields[i].dispatchEvent(new Event("change"));
            }
          }
        });
      } else {
        petitionerFields.forEach((f) => {
          if (f) {
            f.value = "";
            if (f.tagName === "SELECT") f.dispatchEvent(new Event("change"));
          }
        });
      }
    });
  }
  checkPatientFields();

  // File upload handling
  const fileInput = document.getElementById("attachments");
  const dropZone = fileInput?.parentElement;

  window.removeFile = function (index) {
    const dt = new DataTransfer();
    const files = fileInput.files;
    for (let i = 0; i < files.length; i++) {
      if (i !== index) dt.items.add(files[i]);
    }
    fileInput.files = dt.files;
    displayFiles(fileInput.files);
    validateFiles(fileInput.files, translations);
  };

  if (fileInput) {
    fileInput.addEventListener("change", (e) => {
      const newFiles = Array.from(e.target.files);
      const existingFiles = Array.from(fileInput.files);

      // Crear un DataTransfer para combinar archivos existentes con nuevos
      const dt = new DataTransfer();

      // Primero agregar archivos existentes (si los hay)
      existingFiles.forEach((file) => {
        if (!newFiles.includes(file)) {
          dt.items.add(file);
        }
      });

      // Luego agregar los nuevos archivos
      newFiles.forEach((file) => {
        dt.items.add(file);
      });

      // Actualizar el input con todos los archivos
      fileInput.files = dt.files;

      if (validateFiles(fileInput.files, translations)) {
        displayFiles(fileInput.files);
      } else {
        // Si falla validación, mantener solo los archivos anteriores válidos
        const dtPrev = new DataTransfer();
        existingFiles.forEach((file) => {
          if (!newFiles.includes(file)) {
            dtPrev.items.add(file);
          }
        });
        fileInput.files = dtPrev.files;
        displayFiles(fileInput.files);
      }
    });

    if (dropZone) {
      ["dragenter", "dragover", "dragleave", "drop"].forEach((e) => {
        dropZone.addEventListener(
          e,
          (ev) => {
            ev.preventDefault();
            ev.stopPropagation();
          },
          false,
        );
      });

      ["dragenter", "dragover"].forEach((e) => {
        dropZone.addEventListener(
          e,
          () => {
            dropZone.classList.add(
              "border-blue-500",
              "bg-blue-50",
              "dark:bg-blue-900",
            );
          },
          false,
        );
      });

      ["dragleave", "drop"].forEach((e) => {
        dropZone.addEventListener(
          e,
          () => {
            dropZone.classList.remove(
              "border-blue-500",
              "bg-blue-50",
              "dark:bg-blue-900",
            );
          },
          false,
        );
      });

      dropZone.addEventListener(
        "drop",
        (e) => {
          const newFiles = Array.from(e.dataTransfer.files);
          const existingFiles = Array.from(fileInput.files);

          // Combinar archivos existentes con los nuevos
          const dt = new DataTransfer();
          existingFiles.forEach((file) => dt.items.add(file));
          newFiles.forEach((file) => dt.items.add(file));

          fileInput.files = dt.files;

          if (validateFiles(fileInput.files, translations)) {
            displayFiles(fileInput.files);
          } else {
            // Si falla validación, mantener solo los archivos anteriores
            const dtPrev = new DataTransfer();
            existingFiles.forEach((file) => dtPrev.items.add(file));
            fileInput.files = dtPrev.files;
            displayFiles(fileInput.files);
          }
        },
        false,
      );
    }
  }
}

// Reset form after successful submission
function resetFormAfterSuccess() {
  const form = document.getElementById("pqrsf-form");
  if (form) {
    form.reset();
    const fileList = document.getElementById("file-list");
    const fileItems = document.getElementById("file-items");
    if (fileList && fileItems) {
      fileList.classList.add("hidden");
      fileItems.innerHTML = "";
    }
    document.querySelectorAll("input, textarea, select").forEach((input) => {
      input.classList.remove(
        "border-red-500",
        "border-green-500",
        "text-red-600",
        "dark:text-red-500",
      );
      input.classList.add("border-gray-300");
    });
    document.querySelectorAll('[id$="_error"]').forEach((errorElement) => {
      errorElement.classList.add("hidden");
    });
  }
}

document.addEventListener('DOMContentLoaded', function () {
    const checkboxes     = document.querySelectorAll('.chk-enfoque');
    const contenedorOtro = document.getElementById('contenedor_enfoque_diferencial_otro');
    const inputOtro      = document.getElementById('enfoque_diferencial_otro');

    checkboxes.forEach(chk => {
        chk.addEventListener('change', function () {
            const valor = this.value;
            if (valor === '13') {
                const isNingunoChecked = this.checked;
                checkboxes.forEach(box => {
                    if (box.value !== '13') {
                        box.disabled = isNingunoChecked;
                        if (isNingunoChecked) box.checked = false;
                    }
                });

                if (isNingunoChecked) {
                    if (contenedorOtro) {
                        contenedorOtro.classList.add('hidden');
                        inputOtro.required = false;
                        inputOtro.value = '';
                    }
                }
            }

            if (valor === '12' && !this.disabled) {
                if (this.checked) {
                    if (contenedorOtro) {
                        contenedorOtro.classList.remove('hidden');
                        inputOtro.required = true;
                    }
                } else {
                    if (contenedorOtro) {
                        contenedorOtro.classList.add('hidden');
                        inputOtro.required = false;
                        inputOtro.value = '';
                    }
                }
            }
        });
    });

    // document.getElementById('pqrsf-form').addEventListener('submit', function (e) {
    //     const checkboxes = document.querySelectorAll('.chk-enfoque');
    //     const isChecked = Array.from(checkboxes).some(cb => cb.checked);

    //     if (!isChecked) {
    //         e.preventDefault();
    //         alert('Por favor, seleccione al menos una opción de enfoque diferencial.');
    //     }
    // });
});