// Validaciones del lado del cliente
document.addEventListener("DOMContentLoaded", () => {
  // Validación del formulario de login
  const loginForm = document.getElementById("loginForm")
  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      if (!validateLoginForm()) {
        e.preventDefault()
      }
    })
  }

  // Validación del formulario de registro
  const registerForm = document.getElementById("registerForm")
  if (registerForm) {
    registerForm.addEventListener("submit", (e) => {
      if (!validateRegisterForm()) {
        e.preventDefault()
      }
    })
  }

  // Validación del formulario de productos
  const productForm = document.getElementById("productForm")
  if (productForm) {
    productForm.addEventListener("submit", (e) => {
      if (!validateProductForm()) {
        e.preventDefault()
      }
    })
  }

  // Validación del formulario de perfil
  const profileForm = document.getElementById("profileForm")
  if (profileForm) {
    profileForm.addEventListener("submit", (e) => {
      if (!validateProfileForm()) {
        e.preventDefault()
      }
    })
  }

  // Validación en tiempo real
  setupRealTimeValidation()
})

// Función para validar formulario de login
function validateLoginForm() {
  let isValid = true

  const username = document.getElementById("username")
  const password = document.getElementById("password")

  // Limpiar errores previos
  clearErrors()

  // Validar usuario
  if (!username.value.trim()) {
    showError("usernameError", "El usuario es obligatorio")
    isValid = false
  } else if (username.value.trim().length < 3) {
    showError("usernameError", "El usuario debe tener al menos 3 caracteres")
    isValid = false
  }

  // Validar contraseña
  if (!password.value) {
    showError("passwordError", "La contraseña es obligatoria")
    isValid = false
  } else if (password.value.length < 6) {
    showError("passwordError", "La contraseña debe tener al menos 6 caracteres")
    isValid = false
  }

  return isValid
}

// Función para validar formulario de registro
function validateRegisterForm() {
  let isValid = true

  const username = document.getElementById("username")
  const email = document.getElementById("email")
  const fullName = document.getElementById("full_name")
  const password = document.getElementById("password")
  const confirmPassword = document.getElementById("confirm_password")

  // Limpiar errores previos
  clearErrors()

  // Validar usuario
  if (!username.value.trim()) {
    showError("usernameError", "El usuario es obligatorio")
    isValid = false
  } else if (username.value.trim().length < 3) {
    showError("usernameError", "El usuario debe tener al menos 3 caracteres")
    isValid = false
  } else if (!/^[a-zA-Z0-9_]+$/.test(username.value.trim())) {
    showError("usernameError", "El usuario solo puede contener letras, números y guiones bajos")
    isValid = false
  }

  // Validar email
  if (!email.value.trim()) {
    showError("emailError", "El email es obligatorio")
    isValid = false
  } else if (!isValidEmail(email.value.trim())) {
    showError("emailError", "Ingrese un email válido")
    isValid = false
  }

  // Validar nombre completo
  if (!fullName.value.trim()) {
    showError("fullNameError", "El nombre completo es obligatorio")
    isValid = false
  } else if (fullName.value.trim().length < 2) {
    showError("fullNameError", "El nombre debe tener al menos 2 caracteres")
    isValid = false
  }

  // Validar contraseña
  if (!password.value) {
    showError("passwordError", "La contraseña es obligatoria")
    isValid = false
  } else if (password.value.length < 6) {
    showError("passwordError", "La contraseña debe tener al menos 6 caracteres")
    isValid = false
  } else if (!isStrongPassword(password.value)) {
    showError("passwordError", "La contraseña debe contener al menos una letra y un número")
    isValid = false
  }

  // Validar confirmación de contraseña
  if (!confirmPassword.value) {
    showError("confirmPasswordError", "Debe confirmar la contraseña")
    isValid = false
  } else if (password.value !== confirmPassword.value) {
    showError("confirmPasswordError", "Las contraseñas no coinciden")
    isValid = false
  }

  return isValid
}

// Función para validar formulario de productos
function validateProductForm() {
  let isValid = true

  const name = document.getElementById("name")
  const price = document.getElementById("price")
  const categoryId = document.getElementById("category_id")

  // Limpiar errores previos
  clearErrors()

  // Validar nombre del producto
  if (!name.value.trim()) {
    showError("nameError", "El nombre del producto es obligatorio")
    isValid = false
  } else if (name.value.trim().length < 3) {
    showError("nameError", "El nombre debe tener al menos 3 caracteres")
    isValid = false
  }

  // Validar precio
  if (!price.value) {
    showError("priceError", "El precio es obligatorio")
    isValid = false
  } else if (Number.parseFloat(price.value) <= 0) {
    showError("priceError", "El precio debe ser mayor a 0")
    isValid = false
  } else if (Number.parseFloat(price.value) > 999999.99) {
    showError("priceError", "El precio no puede ser mayor a $999,999.99")
    isValid = false
  }

  // Validar categoría
  if (!categoryId.value) {
    showError("categoryError", "Debe seleccionar una categoría")
    isValid = false
  }

  return isValid
}

// Función para validar formulario de perfil
function validateProfileForm() {
  let isValid = true

  const fullName = document.getElementById("full_name")
  const email = document.getElementById("email")
  const currentPassword = document.getElementById("current_password")
  const newPassword = document.getElementById("new_password")
  const confirmPassword = document.getElementById("confirm_password")

  // Limpiar errores previos
  clearErrors()

  // Validar nombre completo
  if (!fullName.value.trim()) {
    showError("fullNameError", "El nombre completo es obligatorio")
    isValid = false
  } else if (fullName.value.trim().length < 2) {
    showError("fullNameError", "El nombre debe tener al menos 2 caracteres")
    isValid = false
  }

  // Validar email
  if (!email.value.trim()) {
    showError("emailError", "El email es obligatorio")
    isValid = false
  } else if (!isValidEmail(email.value.trim())) {
    showError("emailError", "Ingrese un email válido")
    isValid = false
  }

  // Validar cambio de contraseña (si se está intentando cambiar)
  if (newPassword.value || confirmPassword.value || currentPassword.value) {
    if (!currentPassword.value) {
      showError("currentPasswordError", "Debe ingresar su contraseña actual")
      isValid = false
    }

    if (!newPassword.value) {
      showError("newPasswordError", "Debe ingresar la nueva contraseña")
      isValid = false
    } else if (newPassword.value.length < 6) {
      showError("newPasswordError", "La nueva contraseña debe tener al menos 6 caracteres")
      isValid = false
    } else if (!isStrongPassword(newPassword.value)) {
      showError("newPasswordError", "La contraseña debe contener al menos una letra y un número")
      isValid = false
    }

    if (!confirmPassword.value) {
      showError("confirmPasswordError", "Debe confirmar la nueva contraseña")
      isValid = false
    } else if (newPassword.value !== confirmPassword.value) {
      showError("confirmPasswordError", "Las nuevas contraseñas no coinciden")
      isValid = false
    }
  }

  return isValid
}

// Función para configurar validación en tiempo real
function setupRealTimeValidation() {
  // Validación de email en tiempo real
  const emailInputs = document.querySelectorAll('input[type="email"]')
  emailInputs.forEach((input) => {
    input.addEventListener("blur", function () {
      const errorElement = document.getElementById(this.id + "Error")
      if (errorElement) {
        if (this.value && !isValidEmail(this.value)) {
          showError(this.id + "Error", "Ingrese un email válido")
        } else {
          clearError(this.id + "Error")
        }
      }
    })
  })

  // Validación de contraseñas en tiempo real
  const passwordInputs = document.querySelectorAll('input[type="password"]')
  passwordInputs.forEach((input) => {
    if (input.id === "password" || input.id === "new_password") {
      input.addEventListener("input", function () {
        const errorElement = document.getElementById(this.id + "Error")
        if (errorElement && this.value) {
          if (this.value.length < 6) {
            showError(this.id + "Error", "La contraseña debe tener al menos 6 caracteres")
          } else if (!isStrongPassword(this.value)) {
            showError(this.id + "Error", "La contraseña debe contener al menos una letra y un número")
          } else {
            clearError(this.id + "Error")
          }
        }
      })
    }

    if (input.id === "confirm_password") {
      input.addEventListener("input", function () {
        const passwordField = document.getElementById("password") || document.getElementById("new_password")
        const errorElement = document.getElementById(this.id + "Error")
        if (errorElement && this.value && passwordField) {
          if (this.value !== passwordField.value) {
            showError(this.id + "Error", "Las contraseñas no coinciden")
          } else {
            clearError(this.id + "Error")
          }
        }
      })
    }
  })

  // Validación de precios en tiempo real
  const priceInputs = document.querySelectorAll('input[type="number"]')
  priceInputs.forEach((input) => {
    if (input.id === "price") {
      input.addEventListener("input", function () {
        const errorElement = document.getElementById(this.id + "Error")
        if (errorElement && this.value) {
          if (Number.parseFloat(this.value) <= 0) {
            showError(this.id + "Error", "El precio debe ser mayor a 0")
          } else if (Number.parseFloat(this.value) > 999999.99) {
            showError(this.id + "Error", "El precio no puede ser mayor a $999,999.99")
          } else {
            clearError(this.id + "Error")
          }
        }
      })
    }
  })

  // Validación de campos de texto en tiempo real
  const textInputs = document.querySelectorAll('input[type="text"]')
  textInputs.forEach((input) => {
    input.addEventListener("blur", function () {
      const errorElement = document.getElementById(this.id + "Error")
      if (errorElement) {
        if (this.required && !this.value.trim()) {
          showError(this.id + "Error", "Este campo es obligatorio")
        } else if (this.value.trim() && this.value.trim().length < 2) {
          showError(this.id + "Error", "Debe tener al menos 2 caracteres")
        } else {
          clearError(this.id + "Error")
        }
      }
    })
  })
}

// Funciones auxiliares
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

function isStrongPassword(password) {
  // Al menos una letra y un número
  const hasLetter = /[a-zA-Z]/.test(password)
  const hasNumber = /\d/.test(password)
  return hasLetter && hasNumber
}

function showError(elementId, message) {
  const errorElement = document.getElementById(elementId)
  if (errorElement) {
    errorElement.textContent = message
    errorElement.style.display = "block"

    // Agregar clase de error al input
    const inputElement = document.getElementById(elementId.replace("Error", ""))
    if (inputElement) {
      inputElement.style.borderColor = "#e74c3c"
    }
  }
}

function clearError(elementId) {
  const errorElement = document.getElementById(elementId)
  if (errorElement) {
    errorElement.textContent = ""
    errorElement.style.display = "none"

    // Remover clase de error del input
    const inputElement = document.getElementById(elementId.replace("Error", ""))
    if (inputElement) {
      inputElement.style.borderColor = "#ddd"
    }
  }
}

function clearErrors() {
  const errorElements = document.querySelectorAll(".error-message")
  errorElements.forEach((element) => {
    element.textContent = ""
    element.style.display = "none"
  })

  // Resetear bordes de inputs
  const inputs = document.querySelectorAll("input, select, textarea")
  inputs.forEach((input) => {
    input.style.borderColor = "#ddd"
  })
}

// Función para confirmar eliminaciones
function confirmDelete(message) {
  return confirm(message || "¿Está seguro de que desea eliminar este elemento?")
}

// Función para formatear números como moneda
function formatCurrency(amount) {
  return new Intl.NumberFormat("es-MX", {
    style: "currency",
    currency: "USD",
  }).format(amount)
}

// Función para validar números de teléfono
function isValidPhone(phone) {
  const phoneRegex = /^[\d\s\-+$$$$]+$/
  return phoneRegex.test(phone) && phone.replace(/\D/g, "").length >= 10
}

// Prevenir envío de formularios con Enter en campos específicos
document.addEventListener("keypress", (e) => {
  if (e.key === "Enter" && e.target.type !== "submit" && e.target.type !== "textarea") {
    const form = e.target.closest("form")
    if (form) {
      e.preventDefault()
      const submitButton = form.querySelector('button[type="submit"]')
      if (submitButton) {
        submitButton.click()
      }
    }
  }
})

// Función para mostrar/ocultar contraseñas
function togglePasswordVisibility(inputId) {
  const input = document.getElementById(inputId)
  if (input) {
    if (input.type === "password") {
      input.type = "text"
    } else {
      input.type = "password"
    }
  }
}

// Función para limpiar formularios
function resetForm(formId) {
  const form = document.getElementById(formId)
  if (form) {
    form.reset()
    clearErrors()
  }
}

// Auto-resize para textareas
document.addEventListener("input", (e) => {
  if (e.target.tagName === "TEXTAREA") {
    e.target.style.height = "auto"
    e.target.style.height = e.target.scrollHeight + "px"
  }
})

// Función para sanitizar entrada del usuario
function sanitizeInput(input) {
  return input.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "").replace(/[<>]/g, "")
}

// Prevenir ataques XSS básicos
document.addEventListener("input", (e) => {
  if (e.target.type === "text" || e.target.tagName === "TEXTAREA") {
    const sanitized = sanitizeInput(e.target.value)
    if (sanitized !== e.target.value) {
      e.target.value = sanitized
    }
  }
})
