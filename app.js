function initDashboardPage() {
  const requestRows = document.querySelectorAll(".request-row[data-request]");
  if (requestRows.length === 0) return; // not on this page

  const pendingCountEls = document.querySelectorAll("[data-pending-count]");
  const pendingHeaderEl = document.querySelector("[data-pending-header]");

  const notificationBadge = document.querySelector("[data-notification-badge]");
  let previousRemaining = null;

  function triggerNotificationPulse() {
    if (!notificationBadge) return;
    const container = notificationBadge.parentElement;
    if (!container) return;
    container.classList.add("pulse");
    window.setTimeout(() => container.classList.remove("pulse"), 900);
  }

  function refreshPendingCount() {
    const remaining = document.querySelectorAll(".request-row[data-request] .row-actions").length;
    pendingCountEls.forEach((el) => (el.textContent = remaining));
    if (pendingHeaderEl) pendingHeaderEl.textContent = `${remaining} waiting`;
    const introEl = document.querySelector("[data-pending-intro]");
    if (introEl) {
      introEl.textContent = introEl.textContent.replace(/\d+ awaiting/, `${remaining} awaiting`);
    }
    if (notificationBadge) {
      notificationBadge.textContent = remaining;
      const container = notificationBadge.parentElement;
      if (container) container.style.display = remaining > 0 ? "inline-flex" : "none";
      if (previousRemaining !== null && remaining > previousRemaining) {
        triggerNotificationPulse();
      }
    }
    previousRemaining = remaining;
  }

  refreshPendingCount();

  async function sendAppointmentAction(id, action) {
    try {
      const response = await fetch("update_appointment.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(id)}&action=${encodeURIComponent(action)}`,
      });

      const data = await response.json();
      if (!response.ok || !data.success) {
        alert(data.message || "Unable to update appointment.");
        return null;
      }

      return data;
    } catch (error) {
      alert("Unable to update appointment. Please try again.");
      return null;
    }
  }

  requestRows.forEach((row) => {
    const approveBtn = row.querySelector(".btn-approve");
    const rejectBtn = row.querySelector(".btn-reject");

    async function resolve(outcome) {
      const actions = row.querySelector(".row-actions");
      if (!actions) return;
      const id = row.dataset.id;

      const data = await sendAppointmentAction(id, outcome);
      if (!data) return;

      const span = document.createElement("span");
      span.className = outcome === "approved" ? "outcome-approved" : "outcome-rejected";
      span.textContent = outcome === "approved" ? "Approved" : "Rejected";
      actions.replaceWith(span);
      refreshPendingCount();
    }

    if (approveBtn) approveBtn.addEventListener("click", () => resolve("approved"));
    if (rejectBtn) rejectBtn.addEventListener("click", () => resolve("rejected"));
  });

  document.querySelectorAll(".complete-btn").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const row = btn.closest("tr");
      if (!row) return;

      const id = row.dataset.id;
      const statusCell = row.querySelector(".pill");
      const prevStatus = btn.dataset.prevStatus || "Confirmed";
      const isDone = btn.classList.contains("done");

      if (isDone) {
        statusCell.textContent = prevStatus;
        statusCell.className = `pill ${prevStatus === "In room" ? "pill-inroom" : "pill-confirmed"}`;
        btn.classList.remove("done");
        btn.textContent = "Complete";
        return;
      }

      btn.disabled = true;
      const data = await sendAppointmentAction(id, "complete");
      btn.disabled = false;
      if (!data) return;

      statusCell.textContent = "Complete";
      statusCell.className = "pill pill-complete";
      btn.classList.add("done");
      btn.textContent = "Undo";
    });
  });
}

document.addEventListener("DOMContentLoaded", initDashboardPage);
