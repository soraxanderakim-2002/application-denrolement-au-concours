/* ==================== ADMIN DASHBOARD JAVASCRIPT ==================== */

/**
 * Export table to Excel
 */
function exportTableToExcel(tableId, fileName) {
  const table = document.getElementById(tableId);
  if (!table) {
    alert("Tableau non trouvé");
    return;
  }

  try {
    const workbook = XLSX.utils.table_to_book(table);
    XLSX.writeFile(workbook, fileName || "export.xlsx");
  } catch (error) {
    console.error("Erreur lors de l'export Excel:", error);
    alert("Erreur lors de l'export");
  }
}

/**
 * Export table to PDF
 */
function exportTableToPDF(tableId, fileName) {
  const table = document.getElementById(tableId);
  if (!table) {
    alert("Tableau non trouvé");
    return;
  }

  const printWindow = window.open("", "", "height=500,width=900");
  printWindow.document.write("<html><head><title>" + fileName + "</title>");
  printWindow.document.write("<style>");
  printWindow.document.write(
    "body { font-family: Arial, sans-serif; margin: 20px; }"
  );
  printWindow.document.write(
    "table { border-collapse: collapse; width: 100%; }"
  );
  printWindow.document.write(
    "th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }"
  );
  printWindow.document.write("th { background-color: #2c3e50; color: white; }");
  printWindow.document.write(
    "tr:nth-child(even) { background-color: #f8f9fa; }"
  );
  printWindow.document.write("</style></head><body>");
  printWindow.document.write(table.outerHTML);
  printWindow.document.write("</body></html>");
  printWindow.document.close();

  setTimeout(() => {
    printWindow.print();
    printWindow.close();
  }, 100);
}

/**
 * Afficher un candidat
 */
function viewCandidate(id) {
  console.log("Affichage du candidat:", id);
  // Implémenter la logique d'affichage
  // Peut ouvrir un modal avec les détails du candidat
  alert("Affichage du candidat ID: " + id);
}

/**
 * Modifier un candidat
 */
function editCandidate(id) {
  console.log("Modification du candidat:", id);
  // Implémenter la logique de modification
  alert("Modification du candidat ID: " + id);
}

/**
 * Supprimer un candidat
 */
function deleteCandidate(id) {
  if (confirm("Êtes-vous sûr de vouloir supprimer ce candidat?")) {
    console.log("Suppression du candidat:", id);
    // Implémenter la logique de suppression
    alert("Candidat ID: " + id + " a été supprimé");
  }
}

/**
 * Afficher un paiement
 */
function viewPayment(btn) {
  const row = btn.closest("tr");
  const paymentId = row.querySelector("td:first-child").textContent;
  console.log("Affichage du paiement:", paymentId);
  alert("Affichage du paiement: " + paymentId);
}

/**
 * Approuver un paiement
 */
function approvePayment(btn) {
  const row = btn.closest("tr");
  const paymentId = row.querySelector("td:first-child").textContent;

  if (confirm("Êtes-vous sûr de vouloir approuver ce paiement?")) {
    console.log("Approbation du paiement:", paymentId);
    alert("Paiement " + paymentId + " approuvé");
    // Mettre à jour le statut dans l'interface
    const statusCell = row.querySelector("td:nth-child(5)");
    if (statusCell) {
      statusCell.innerHTML =
        '<span class="badge badge-success"><i class="bi bi-check-circle"></i> Validé</span>';
    }
  }
}

/**
 * Rejeter un paiement
 */
function rejectPayment(btn) {
  const row = btn.closest("tr");
  const paymentId = row.querySelector("td:first-child").textContent;

  if (confirm("Êtes-vous sûr de vouloir rejeter ce paiement?")) {
    console.log("Rejet du paiement:", paymentId);
    alert("Paiement " + paymentId + " rejeté");
    // Mettre à jour le statut dans l'interface
    const statusCell = row.querySelector("td:nth-child(5)");
    if (statusCell) {
      statusCell.innerHTML =
        '<span class="badge badge-danger"><i class="bi bi-x-circle"></i> Rejeté</span>';
    }
  }
}

/**
 * Filtrer le tableau par statut
 */
function filterByStatus(status) {
  const rows = document.querySelectorAll("tbody tr");
  rows.forEach((row) => {
    const statusCell = row.querySelector("td:nth-child(7)");
    if (status === "all" || statusCell.textContent.includes(status)) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

/**
 * Rechercher dans le tableau
 */
function searchTable(inputId, tableId) {
  const input = document.getElementById(inputId);
  const table = document.getElementById(tableId);
  const rows = table
    .getElementsByTagName("tbody")[0]
    .getElementsByTagName("tr");

  input.addEventListener("keyup", function () {
    const searchTerm = input.value.toLowerCase();

    rows.forEach((row) => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(searchTerm) ? "" : "none";
    });
  });
}

/**
 * Tri du tableau
 */
function sortTable(columnIndex, tableId) {
  const table = document.getElementById(tableId);
  const tbody = table.getElementsByTagName("tbody")[0];
  const rows = Array.from(tbody.getElementsByTagName("tr"));

  rows.sort((a, b) => {
    const aValue = a.cells[columnIndex].textContent.trim();
    const bValue = b.cells[columnIndex].textContent.trim();

    // Essayer de comparer comme nombres
    if (!isNaN(aValue) && !isNaN(bValue)) {
      return parseFloat(aValue) - parseFloat(bValue);
    }

    // Comparer comme texte
    return aValue.localeCompare(bValue);
  });

  rows.forEach((row) => tbody.appendChild(row));
}

/**
 * Formater la devise
 */
function formatCurrency(amount) {
  return new Intl.NumberFormat("fr-CM", {
    style: "currency",
    currency: "XAF",
  }).format(amount);
}

/**
 * Initialiser le dashboard
 */
document.addEventListener("DOMContentLoaded", function () {
  console.log("Dashboard initialisé");

  // Initialiser les tooltips Bootstrap
  if (typeof bootstrap !== "undefined") {
    const tooltipTriggerList = [].slice.call(
      document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  }

  // Initialiser les popovers Bootstrap
  if (typeof bootstrap !== "undefined") {
    const popoverTriggerList = [].slice.call(
      document.querySelectorAll('[data-bs-toggle="popover"]')
    );
    popoverTriggerList.map(function (popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl);
    });
  }
});

/**
 * Afficher un message de succès
 */
function showSuccessMessage(message, timeout = 3000) {
  const alert = document.createElement("div");
  alert.className = "alert alert-success alert-dismissible fade show";
  alert.setAttribute("role", "alert");
  alert.innerHTML = '<i class="bi bi-check-circle"></i> ' + message;
  alert.innerHTML +=
    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';

  document
    .querySelector(".main-content")
    .insertBefore(alert, document.querySelector(".main-content").firstChild);

  setTimeout(() => {
    alert.remove();
  }, timeout);
}

/**
 * Afficher un message d'erreur
 */
function showErrorMessage(message, timeout = 5000) {
  const alert = document.createElement("div");
  alert.className = "alert alert-danger alert-dismissible fade show";
  alert.setAttribute("role", "alert");
  alert.innerHTML = '<i class="bi bi-exclamation-circle"></i> ' + message;
  alert.innerHTML +=
    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';

  document
    .querySelector(".main-content")
    .insertBefore(alert, document.querySelector(".main-content").firstChild);

  setTimeout(() => {
    alert.remove();
  }, timeout);
}

/**
 * Charger les données via AJAX
 */
function loadData(url, callback) {
  fetch(url)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Erreur réseau");
      }
      return response.json();
    })
    .then((data) => {
      if (typeof callback === "function") {
        callback(data);
      }
    })
    .catch((error) => {
      console.error("Erreur:", error);
      showErrorMessage("Erreur lors du chargement des données");
    });
}

/**
 * Envoyer les données via AJAX
 */
function submitData(url, data, callback) {
  fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Erreur réseau");
      }
      return response.json();
    })
    .then((result) => {
      if (typeof callback === "function") {
        callback(result);
      }
    })
    .catch((error) => {
      console.error("Erreur:", error);
      showErrorMessage("Erreur lors de l'envoi des données");
    });
}
