const byId = (id) => document.getElementById(id);
const money = (value) =>
    `Rp${new Intl.NumberFormat("id-ID", { maximumFractionDigits: 0 }).format(value)}`;
const daysBetween = (start, end) => {
    if (!start || !end) return 0;
    const date = (value) =>
        Date.UTC(
            ...value.split("-").map((v, i) => Number(v) - (i === 1 ? 1 : 0)),
        );
    return Math.floor((date(end) - date(start)) / 86400000) + 1;
};
document.querySelectorAll("[data-toggle]").forEach((button) =>
    button.addEventListener("click", () => {
        const target = byId(button.dataset.toggle);
        button.setAttribute(
            "aria-expanded",
            String(target.classList.toggle("open")),
        );
    }),
);
const sidebar = byId("owner-sidebar");
const toggleSidebar = (open) => {
    sidebar?.classList.toggle("open", open);
    byId("sidebar-overlay")?.classList.toggle("open", open);
    document
        .querySelector("[data-sidebar-open]")
        ?.setAttribute("aria-expanded", String(open));
    if (sidebar)
        sidebar.inert =
            !open && window.matchMedia("(max-width: 1023px)").matches;
    if (open) sidebar?.querySelector("[data-sidebar-close]")?.focus();
};
document
    .querySelector("[data-sidebar-open]")
    ?.addEventListener("click", () => toggleSidebar(true));
document
    .querySelector("[data-sidebar-close]")
    ?.addEventListener("click", () => toggleSidebar(false));
byId("sidebar-overlay")?.addEventListener("click", () => toggleSidebar(false));
if (sidebar) {
    const mobile = window.matchMedia("(max-width: 1023px)");
    const syncSidebar = () => toggleSidebar(false);
    mobile.addEventListener("change", syncSidebar);
    syncSidebar();
}
document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
        toggleSidebar(false);
        byId("public-nav")?.classList.remove("open");
        document
            .querySelector('[data-toggle="public-nav"]')
            ?.setAttribute("aria-expanded", "false");
    }
});
document
    .querySelector("[data-filter-toggle]")
    ?.addEventListener("click", (event) => {
        const open = byId("filters").classList.toggle("expanded");
        event.currentTarget.setAttribute("aria-expanded", String(open));
    });
document.querySelectorAll("[data-confirm]").forEach((form) =>
    form.addEventListener("submit", (event) => {
        if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    }),
);
document.querySelectorAll('a[aria-disabled="true"]').forEach((link) =>
    link.addEventListener("click", (event) => {
        if (link.getAttribute("aria-disabled") === "true")
            event.preventDefault();
    }),
);
const estimateForm = document.querySelector("[data-estimate-form]");
if (estimateForm) {
    let pending;
    let revision = 0;
    const wa = byId("booking-wa");
    const message = byId("availability");
    const clearLink = () => {
        wa.removeAttribute("href");
        wa.setAttribute("aria-disabled", "true");
        wa.setAttribute("tabindex", "-1");
    };
    const update = async () => {
        pending?.abort();
        const current = ++revision;
        clearLink();
        byId("estimate-days").textContent = "";
        ["estimate-rent", "estimate-driver", "estimate-total"].forEach((id) => {
            byId(id).textContent = "—";
        });
        const data = new FormData(estimateForm);
        const start = data.get("tgl_ambil");
        const end = data.get("tgl_kembali");
        estimateForm.elements.tgl_kembali.min =
            start || estimateForm.elements.tgl_ambil.min;
        if (!start || !end) {
            if (!start && !end && estimateForm.dataset.inquiryUrl) {
                wa.href = estimateForm.dataset.inquiryUrl;
                wa.target = "_blank";
                wa.rel = "noopener noreferrer";
                wa.removeAttribute("aria-disabled");
                wa.removeAttribute("tabindex");
            }
            message.textContent = "Pilih tanggal sewa terlebih dahulu.";
            message.className = "availability neutral";
            byId("booking").classList.remove("has-dates");
            return;
        }
        if (!estimateForm.checkValidity()) {
            message.textContent =
                "Periksa tanggal sewa. Tanggal ambil tidak boleh di masa lalu dan tanggal kembali tidak sebelum tanggal ambil.";
            message.className = "availability error";
            return;
        }
        pending = new AbortController();
        message.className = "availability neutral";
        message.textContent = "Memeriksa ketersediaan…";
        try {
            const response = await fetch(
                `${estimateForm.dataset.estimateUrl}?${new URLSearchParams(data)}`,
                {
                    headers: { Accept: "application/json" },
                    signal: pending.signal,
                },
            );
            const result = await response.json();
            if (current !== revision) return;
            if (!response.ok)
                throw new Error(
                    Object.values(result.errors || {}).flat()[0] ||
                        "Ketersediaan belum dapat diperiksa. Coba lagi.",
                );
            byId("booking").classList.add("has-dates");
            byId("estimate-days").textContent = `· ${result.lama} hari`;
            byId("estimate-rent").textContent = result.sewa;
            byId("estimate-driver").textContent = result.sopir;
            byId("estimate-total").textContent = result.total;
            message.className = `availability ${result.tersedia ? "" : "error"}`;
            message.textContent = result.tersedia
                ? "Tersedia pada tanggal pilihan Anda."
                : "Mobil tidak tersedia pada tanggal tersebut. Coba tanggal atau mobil lain.";
            if (result.tersedia && result.wa) {
                wa.href = result.wa;
                wa.target = "_blank";
                wa.rel = "noopener noreferrer";
                wa.removeAttribute("aria-disabled");
                wa.removeAttribute("tabindex");
            }
            history.replaceState(
                null,
                "",
                `${estimateForm.action}?${new URLSearchParams(data)}`,
            );
        } catch (error) {
            if (error.name !== "AbortError" && current === revision) {
                message.textContent =
                    error.message || "Terjadi kendala koneksi. Coba kembali.";
                message.className = "availability error";
            }
        }
    };
    estimateForm.addEventListener("change", update);
    estimateForm.addEventListener("submit", (event) => {
        event.preventDefault();
        update();
    });
    window.addEventListener("pageshow", (event) => {
        if (event.persisted) update();
    });
}
const orderForm = document.querySelector("[data-order-form]");
if (orderForm) {
    const updateOrder = () => {
        const fields = orderForm.elements;
        const option = fields.mobil_id.selectedOptions[0];
        const days = daysBetween(
            fields.tgl_ambil.value,
            fields.tgl_kembali.value,
        );
        const needsAgreedPrice = !!fields.mobil_id.value && !option?.dataset.price && !orderForm.dataset.snapshotPrice;
        if (fields.harga_kesepakatan) {
            fields.harga_kesepakatan.required = needsAgreedPrice;
            fields.harga_kesepakatan.disabled = !needsAgreedPrice;
        }
        const price = orderForm.dataset.snapshotPrice || option?.dataset.price || fields.harga_kesepakatan?.value;
        byId("new-customer").hidden = !!fields.pelanggan_id.value;
        fields.nama_pelanggan.required = !fields.pelanggan_id.value;
        fields.no_hp_pelanggan.required = !fields.pelanggan_id.value;
        if (option?.dataset.photo) {
            byId("order-photo").src = option.dataset.photo;
            byId("order-photo").alt = option.dataset.title;
            byId("order-car").textContent = option.dataset.title;
        }
        if (!price || days < 1 || !Number.isFinite(days)) {
            ["order-days", "order-rent", "order-driver", "order-total"].forEach(
                (id) => {
                    byId(id).textContent = "—";
                },
            );
            return;
        }
        const rent = Number(price) * days;
        const driver =
            fields.jenis_sewa.value === "dengan_sopir"
                ? Number(
                      orderForm.dataset.snapshotDriver ||
                          orderForm.dataset.driverRate,
                  ) * days
                : 0;
        byId("order-days").textContent = `${days} hari`;
        byId("order-rent").textContent = money(rent);
        byId("order-driver").textContent = money(driver);
        byId("order-total").textContent = money(rent + driver);
    };
    orderForm.addEventListener("change", updateOrder);
    orderForm.addEventListener("input", updateOrder);
    updateOrder();
}
