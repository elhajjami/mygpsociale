import { useState } from "react";

const COLORS = {
  bg: "#0f1117",
  surface: "#1a1d2e",
  card: "#20243a",
  border: "#2e3352",
  accent: "#4f8ef7",
  accentSoft: "#1e3a6e",
  success: "#34d399",
  warning: "#fbbf24",
  text: "#e2e8f0",
  muted: "#64748b",
  white: "#ffffff",
};

const styles = {
  app: {
    minHeight: "100vh",
    background: COLORS.bg,
    color: COLORS.text,
    fontFamily: "'IBM Plex Sans', 'Segoe UI', sans-serif",
    padding: "0",
  },
  header: {
    background: `linear-gradient(135deg, ${COLORS.surface} 0%, #0d1526 100%)`,
    borderBottom: `1px solid ${COLORS.border}`,
    padding: "20px 32px",
    display: "flex",
    alignItems: "center",
    gap: "16px",
  },
  headerIcon: {
    width: 42,
    height: 42,
    background: `linear-gradient(135deg, ${COLORS.accent}, #6366f1)`,
    borderRadius: 10,
    display: "flex",
    alignItems: "center",
    justifyContent: "center",
    fontSize: 20,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 700,
    color: COLORS.white,
    letterSpacing: "-0.3px",
  },
  headerSub: {
    fontSize: 12,
    color: COLORS.muted,
    marginTop: 2,
  },
  main: {
    maxWidth: 1100,
    margin: "0 auto",
    padding: "32px 24px",
  },
  typeSelector: {
    display: "grid",
    gridTemplateColumns: "1fr 1fr",
    gap: 12,
    marginBottom: 28,
  },
  typeCard: (active) => ({
    background: active ? COLORS.accentSoft : COLORS.card,
    border: `2px solid ${active ? COLORS.accent : COLORS.border}`,
    borderRadius: 12,
    padding: "16px 20px",
    cursor: "pointer",
    transition: "all 0.2s",
    display: "flex",
    alignItems: "center",
    gap: 12,
  }),
  typeIcon: (active) => ({
    width: 40,
    height: 40,
    borderRadius: 8,
    background: active ? COLORS.accent : COLORS.border,
    display: "flex",
    alignItems: "center",
    justifyContent: "center",
    fontSize: 18,
    flexShrink: 0,
  }),
  typeLabel: (active) => ({
    fontWeight: 600,
    color: active ? COLORS.white : COLORS.text,
    fontSize: 14,
  }),
  typeSub: {
    fontSize: 11,
    color: COLORS.muted,
    marginTop: 2,
  },
  formCard: {
    background: COLORS.card,
    border: `1px solid ${COLORS.border}`,
    borderRadius: 14,
    padding: "24px",
    marginBottom: 20,
  },
  sectionTitle: {
    fontSize: 13,
    fontWeight: 600,
    color: COLORS.muted,
    textTransform: "uppercase",
    letterSpacing: "0.08em",
    marginBottom: 16,
    display: "flex",
    alignItems: "center",
    gap: 8,
  },
  grid2: {
    display: "grid",
    gridTemplateColumns: "1fr 1fr",
    gap: 14,
  },
  grid3: {
    display: "grid",
    gridTemplateColumns: "1fr 1fr 1fr",
    gap: 14,
  },
  field: {
    display: "flex",
    flexDirection: "column",
    gap: 5,
  },
  label: {
    fontSize: 11,
    fontWeight: 600,
    color: COLORS.muted,
    textTransform: "uppercase",
    letterSpacing: "0.05em",
  },
  input: {
    background: COLORS.surface,
    border: `1px solid ${COLORS.border}`,
    borderRadius: 8,
    padding: "9px 12px",
    color: COLORS.text,
    fontSize: 13,
    outline: "none",
    transition: "border-color 0.15s",
  },
  select: {
    background: COLORS.surface,
    border: `1px solid ${COLORS.border}`,
    borderRadius: 8,
    padding: "9px 12px",
    color: COLORS.text,
    fontSize: 13,
    outline: "none",
    cursor: "pointer",
  },
  tableHeader: {
    display: "grid",
    gap: 1,
    background: COLORS.border,
    borderRadius: "8px 8px 0 0",
    overflow: "hidden",
    marginBottom: 1,
  },
  th: {
    background: "#252a42",
    padding: "9px 12px",
    fontSize: 11,
    fontWeight: 700,
    color: COLORS.muted,
    textTransform: "uppercase",
    letterSpacing: "0.05em",
  },
  tableRow: {
    display: "grid",
    gap: 1,
    background: COLORS.border,
    marginBottom: 1,
  },
  td: {
    background: COLORS.surface,
    padding: "6px 8px",
  },
  tdInput: {
    width: "100%",
    background: "transparent",
    border: "none",
    color: COLORS.text,
    fontSize: 12,
    outline: "none",
    padding: "2px 4px",
  },
  addRowBtn: {
    background: "transparent",
    border: `1px dashed ${COLORS.border}`,
    borderRadius: 8,
    padding: "8px",
    color: COLORS.muted,
    cursor: "pointer",
    fontSize: 12,
    width: "100%",
    marginTop: 6,
    transition: "all 0.15s",
  },
  btn: {
    background: `linear-gradient(135deg, ${COLORS.accent}, #6366f1)`,
    border: "none",
    borderRadius: 10,
    padding: "13px 28px",
    color: COLORS.white,
    fontSize: 14,
    fontWeight: 700,
    cursor: "pointer",
    display: "flex",
    alignItems: "center",
    gap: 8,
    letterSpacing: "0.02em",
  },
  btnSecondary: {
    background: COLORS.card,
    border: `1px solid ${COLORS.border}`,
    borderRadius: 10,
    padding: "13px 20px",
    color: COLORS.text,
    fontSize: 13,
    fontWeight: 600,
    cursor: "pointer",
  },
  resultBox: {
    background: COLORS.card,
    border: `1px solid ${COLORS.border}`,
    borderRadius: 14,
    padding: "28px",
    marginTop: 20,
    position: "relative",
  },
  resultBadge: {
    position: "absolute",
    top: 16,
    right: 16,
    background: `${COLORS.success}20`,
    border: `1px solid ${COLORS.success}40`,
    borderRadius: 6,
    padding: "3px 10px",
    fontSize: 11,
    color: COLORS.success,
    fontWeight: 700,
  },
  resultText: {
    fontSize: 13,
    lineHeight: 1.8,
    color: COLORS.text,
    whiteSpace: "pre-wrap",
    fontFamily: "'IBM Plex Mono', monospace",
  },
  loader: {
    display: "flex",
    alignItems: "center",
    gap: 12,
    color: COLORS.accent,
    fontSize: 14,
    padding: "20px 0",
  },
  spinner: {
    width: 20,
    height: 20,
    border: `2px solid ${COLORS.accentSoft}`,
    borderTop: `2px solid ${COLORS.accent}`,
    borderRadius: "50%",
    animation: "spin 0.8s linear infinite",
  },
  statusBar: {
    display: "flex",
    alignItems: "center",
    gap: 8,
    padding: "10px 14px",
    borderRadius: 8,
    marginBottom: 16,
    fontSize: 12,
  },
  tag: (color) => ({
    display: "inline-flex",
    alignItems: "center",
    gap: 4,
    background: `${color}15`,
    border: `1px solid ${color}30`,
    borderRadius: 5,
    padding: "2px 8px",
    fontSize: 11,
    color: color,
    fontWeight: 600,
  }),
  divider: {
    height: 1,
    background: COLORS.border,
    margin: "16px 0",
  },
  totalRow: {
    display: "flex",
    justifyContent: "space-between",
    alignItems: "center",
    padding: "10px 0",
    borderTop: `1px solid ${COLORS.border}`,
    marginTop: 8,
  },
};

const PARTNER_TYPES = [
  {
    id: "medical",
    label: "Formation Médicale",
    sub: "Médecin, Laboratoire, Radiologie",
    icon: "🏥",
    form: 1,
  },
  {
    id: "clinique",
    label: "Clinique",
    sub: "Hospitalisation, Bloc opératoire",
    icon: "🏨",
    form: 2,
  },
];

const EMPTY_LINE = () => ({
  id: Date.now() + Math.random(),
  matricule: "",
  nom: "",
  beneficiaire: "",
  nature: "",
  cotation: "",
  tarif: "",
});

const EMPTY_PRESTATION = () => ({
  id: Date.now() + Math.random(),
  designation: "",
  lettresCle: "",
  nbre: "",
  prixUnitaire: "",
  montant: "",
});

export default function App() {
  const [partnerType, setPartnerType] = useState("medical");
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState("");
  const [streamText, setStreamText] = useState("");

  // Formulaire 1 — Medical
  const [medInfo, setMedInfo] = useState({
    nom: "",
    adresse: "",
    ville: "",
    tel: "",
    fax: "",
    rib: "",
    agence: "",
    patente: "",
    if_: "",
    cnss: "",
    ice: "",
    dateFacture: new Date().toISOString().slice(0, 10),
    numFacture: "",
  });
  const [medLines, setMedLines] = useState([EMPTY_LINE(), EMPTY_LINE(), EMPTY_LINE()]);

  // Formulaire 2 — Clinique
  const [clinInfo, setClinInfo] = useState({
    nomClinique: "",
    ice: "003659658000030",
    numFacture: "",
    dateFacture: new Date().toISOString().slice(0, 10),
    nomPatient: "",
    hospitalisationDu: "",
    hospitalisationAu: "",
    adresse: "",
    tel: "",
    fax: "",
    rib: "",
    agence: "",
    patente: "",
    if_: "",
    cnss: "",
  });
  const [prestationsClinic, setPrestationsClinic] = useState([
    { id: 1, designation: "SEJOUR", lettresCle: "", nbre: "", prixUnitaire: "", montant: "" },
    { id: 2, designation: "BLOC OPERATOIRE", lettresCle: "K", nbre: "", prixUnitaire: "", montant: "" },
    { id: 3, designation: "", lettresCle: "", nbre: "", prixUnitaire: "", montant: "" },
    { id: 4, designation: "PHARMACIE (Médicale/Chirurgicale)", lettresCle: "", nbre: "", prixUnitaire: "", montant: "" },
  ]);
  const [honoraires, setHonoraires] = useState([
    { id: 1, designation: "MEDECIN (chirurgien)", lettresCle: "K", nbre: "", prixUnitaire: "", montant: "" },
    { id: 2, designation: "MEDECIN (Anesthésiste)", lettresCle: "K", nbre: "", prixUnitaire: "", montant: "" },
    { id: 3, designation: "AUTRES MEDECINS", lettresCle: "K", nbre: "", prixUnitaire: "", montant: "" },
    { id: 4, designation: "LABORATOIRE (Analyses)", lettresCle: "B", nbre: "", prixUnitaire: "", montant: "" },
    { id: 5, designation: "ANAPATH", lettresCle: "P", nbre: "", prixUnitaire: "", montant: "" },
    { id: 6, designation: "+RADIOLOGIE (Examens)", lettresCle: "K/Z", nbre: "", prixUnitaire: "", montant: "" },
  ]);

  const updateMedLine = (id, field, val) =>
    setMedLines((l) => l.map((r) => (r.id === id ? { ...r, [field]: val } : r)));

  const updatePrestation = (setter, id, field, val) =>
    setter((l) => l.map((r) => (r.id === id ? { ...r, [field]: val } : r)));

  const sumMedical = () =>
    medLines.reduce((acc, r) => acc + (parseFloat(r.tarif) || 0), 0).toFixed(2);

  const sumClinic = () =>
    prestationsClinic.reduce((acc, r) => acc + (parseFloat(r.montant) || 0), 0).toFixed(2);

  const sumHonoraires = () =>
    honoraires.reduce((acc, r) => acc + (parseFloat(r.montant) || 0), 0).toFixed(2);

  const buildPrompt = () => {
    if (partnerType === "medical") {
      const lines = medLines.filter((l) => l.matricule || l.nom || l.nature);
      return `Tu es un assistant de gestion médicale. À partir des données ci-dessous d'une prise en charge validée, génère une facture médicale structurée et professionnelle (Formulaire 1 - Formation Médicale).

**Données du prestataire:**
- Nom: ${medInfo.nom || "N/A"}
- Adresse: ${medInfo.adresse}, ${medInfo.ville}
- Tél: ${medInfo.tel} | Fax: ${medInfo.fax}
- RIB: ${medInfo.rib} | Agence: ${medInfo.agence}
- N° Patente: ${medInfo.patente}
- N° IF: ${medInfo.if_}
- N° CNSS: ${medInfo.cnss}
- ICE: ${medInfo.ice}
- N° Facture: ${medInfo.numFacture}
- Date Facture: ${medInfo.dateFacture}

**Lignes de facturation:**
${lines.map((l, i) => `${i + 1}. Matricule: ${l.matricule} | Patient: ${l.nom} | Bénéficiaire: ${l.beneficiaire} | Acte: ${l.nature} | Cotation: ${l.cotation} | Tarif TTC: ${l.tarif} DH`).join("\n")}

**Total:** ${sumMedical()} DH

Génère:
1. Un récapitulatif de facturation clair
2. Le montant total en lettres (en dirhams marocains)
3. Une vérification de cohérence (cotations, montants)
4. Les informations pour la signature et le cachet
5. Tout avertissement si des données manquantes ou incohérentes

Réponds en français de façon professionnelle et structurée.`;
    } else {
      const prestFiltered = prestationsClinic.filter((p) => p.nbre || p.montant);
      const honFiltered = honoraires.filter((h) => h.nbre || h.montant);
      return `Tu es un assistant de gestion médicale. À partir des données ci-dessous d'une prise en charge clinique validée, génère une facture structurée et professionnelle (Formulaire 2 - Clinique).

**Informations Clinique:**
- Nom: ${clinInfo.nomClinique || "N/A"}
- ICE: ${clinInfo.ice}
- N° Facture: ${clinInfo.numFacture}
- Date Facture: ${clinInfo.dateFacture}
- Patient: ${clinInfo.nomPatient}
- Hospitalisation: du ${clinInfo.hospitalisationDu} au ${clinInfo.hospitalisationAu}
- Adresse: ${clinInfo.adresse} | Tél: ${clinInfo.tel} | Fax: ${clinInfo.fax}
- RIB: ${clinInfo.rib} | Agence: ${clinInfo.agence}
- Patente: ${clinInfo.patente} | IF: ${clinInfo.if_} | CNSS: ${clinInfo.cnss}

**Prestations Clinique:**
${prestFiltered.map((p) => `- ${p.designation} | Lettre Clé: ${p.lettresCle || "-"} | Nbre: ${p.nbre} | Prix Unitaire: ${p.prixUnitaire} DH | Montant: ${p.montant} DH`).join("\n")}
TOTAL CLINIQUE: ${sumClinic()} DH

**Honoraires & Autres Prestations:**
${honFiltered.map((h) => `- ${h.designation} | ${h.lettresCle} | Nbre: ${h.nbre} | Prix: ${h.prixUnitaire} DH | Montant: ${h.montant} DH`).join("\n")}
TOTAL AUTRES PRESTATIONS: ${sumHonoraires()} DH

TOTAL GÉNÉRAL: ${(parseFloat(sumClinic()) + parseFloat(sumHonoraires())).toFixed(2)} DH

Génère:
1. Un récapitulatif complet de la facturation clinique
2. Le total général en lettres (dirhams marocains)
3. La répartition Part Adhérent / Part CNOPS (si applicable)
4. Une vérification de cohérence des montants et des actes
5. Tout avertissement pour données manquantes ou incohérentes

Réponds en français de façon professionnelle et structurée.`;
    }
  };

  const generate = async () => {
    setLoading(true);
    setResult("");
    setStreamText("");
    try {
      const response = await fetch("https://api.anthropic.com/v1/messages", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          model: "claude-sonnet-4-20250514",
          max_tokens: 1000,
          stream: true,
          messages: [{ role: "user", content: buildPrompt() }],
        }),
      });

      const reader = response.body.getReader();
      const decoder = new TextDecoder();
      let full = "";

      while (true) {
        const { done, value } = await reader.read();
        if (done) break;
        const chunk = decoder.decode(value);
        const lines = chunk.split("\n");
        for (const line of lines) {
          if (line.startsWith("data: ")) {
            const data = line.slice(6);
            if (data === "[DONE]") continue;
            try {
              const parsed = JSON.parse(data);
              if (parsed.type === "content_block_delta" && parsed.delta?.text) {
                full += parsed.delta.text;
                setStreamText(full);
              }
            } catch {}
          }
        }
      }
      setResult(full);
    } catch (e) {
      setResult("Erreur lors de la génération. Vérifiez votre connexion.");
    }
    setLoading(false);
  };

  const colsMed = ["1.2fr", "2fr", "1.5fr", "2fr", "1fr", "1.2fr"];
  const colsClinic = ["2.5fr", "0.8fr", "0.8fr", "1.2fr", "1.2fr"];

  return (
    <div style={styles.app}>
      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono&display=swap');
        * { box-sizing: border-box; margin: 0; padding: 0; }
        input::placeholder { color: #3a4060; }
        input:focus, select:focus { border-color: #4f8ef7 !important; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.3s ease; }
        button:hover { opacity: 0.9; }
        .add-row:hover { border-color: #4f8ef7 !important; color: #4f8ef7 !important; }
      `}</style>

      <div style={styles.header}>
        <div style={styles.headerIcon}>🏥</div>
        <div>
          <div style={styles.headerTitle}>Facturation — Prise en Charge</div>
          <div style={styles.headerSub}>Génération assistée par IA · CNOPS / CNSS</div>
        </div>
        <div style={{ marginLeft: "auto", display: "flex", gap: 8 }}>
          <span style={styles.tag(COLORS.success)}>✓ Prise en charge validée</span>
          <span style={styles.tag(COLORS.warning)}>⚡ Étape facturation</span>
        </div>
      </div>

      <div style={styles.main}>
        {/* Type selector */}
        <div style={{ marginBottom: 8, fontSize: 12, color: COLORS.muted, fontWeight: 600, textTransform: "uppercase", letterSpacing: "0.08em" }}>
          Type de partenaire
        </div>
        <div style={styles.typeSelector}>
          {PARTNER_TYPES.map((t) => (
            <div key={t.id} style={styles.typeCard(partnerType === t.id)} onClick={() => setPartnerType(t.id)}>
              <div style={styles.typeIcon(partnerType === t.id)}>{t.icon}</div>
              <div>
                <div style={styles.typeLabel(partnerType === t.id)}>{t.label}</div>
                <div style={styles.typeSub}>{t.sub}</div>
              </div>
              <div style={{ marginLeft: "auto", fontSize: 11, color: COLORS.muted }}>
                Formulaire {t.form}
              </div>
            </div>
          ))}
        </div>

        {/* FORMULAIRE 1 — MEDICAL */}
        {partnerType === "medical" && (
          <div className="fade-in">
            <div style={styles.formCard}>
              <div style={styles.sectionTitle}>🏷️ Informations Formation Médicale</div>
              <div style={{ ...styles.grid2, marginBottom: 14 }}>
                <div style={styles.field}>
                  <label style={styles.label}>Nom / Raison Sociale</label>
                  <input style={styles.input} value={medInfo.nom} onChange={e => setMedInfo({ ...medInfo, nom: e.target.value })} placeholder="Dr. Martin, Labo Atlas..." />
                </div>
                <div style={styles.field}>
                  <label style={styles.label}>Adresse</label>
                  <input style={styles.input} value={medInfo.adresse} onChange={e => setMedInfo({ ...medInfo, adresse: e.target.value })} placeholder="12 Rue des Orangers" />
                </div>
                <div style={styles.field}>
                  <label style={styles.label}>Ville</label>
                  <input style={styles.input} value={medInfo.ville} onChange={e => setMedInfo({ ...medInfo, ville: e.target.value })} placeholder="Casablanca" />
                </div>
                <div style={styles.field}>
                  <label style={styles.label}>Téléphone</label>
                  <input style={styles.input} value={medInfo.tel} onChange={e => setMedInfo({ ...medInfo, tel: e.target.value })} placeholder="0522..." />
                </div>
                <div style={styles.field}>
                  <label style={styles.label}>N° Facture</label>
                  <input style={styles.input} value={medInfo.numFacture} onChange={e => setMedInfo({ ...medInfo, numFacture: e.target.value })} placeholder="FAC-2025-001" />
                </div>
                <div style={styles.field}>
                  <label style={styles.label}>Date Facture</label>
                  <input type="date" style={styles.input} value={medInfo.dateFacture} onChange={e => setMedInfo({ ...medInfo, dateFacture: e.target.value })} />
                </div>
                <div style={styles.field}>
                  <label style={styles.label}>RIB (24 pos.)</label>
                  <input style={styles.input} value={medInfo.rib} onChange={e => setMedInfo({ ...medInfo, rib: e.target.value })} placeholder="000000000000000000000000" />
                </div>
                <div style={styles.field}>
                  <label style={styles.label}>ICE</label>
                  <input style={styles.input} value={medInfo.ice} onChange={e => setMedInfo({ ...medInfo, ice: e.target.value })} placeholder="000000000000000" />
                </div>
              </div>
            </div>

            {/* Table lignes */}
            <div style={styles.formCard}>
              <div style={styles.sectionTitle}>📋 Lignes de Facturation</div>
              {/* Header */}
              <div style={{ display: "grid", gridTemplateColumns: colsMed.join(" "), background: "#252a42", borderRadius: "8px 8px 0 0", overflow: "hidden", marginBottom: 1 }}>
                {["Matricule", "Nom & Prénom", "Bénéficiaire", "Nature d'examen", "Cotation", "Tarif TTC (DH)"].map(h => (
                  <div key={h} style={styles.th}>{h}</div>
                ))}
              </div>
              {medLines.map((row, i) => (
                <div key={row.id} style={{ display: "grid", gridTemplateColumns: colsMed.join(" "), background: COLORS.border, marginBottom: 1 }}>
                  {["matricule", "nom", "beneficiaire", "nature", "cotation", "tarif"].map(f => (
                    <div key={f} style={styles.td}>
                      <input
                        style={styles.tdInput}
                        value={row[f]}
                        onChange={e => updateMedLine(row.id, f, e.target.value)}
                        placeholder={f === "tarif" ? "0.00" : "—"}
                        type={f === "tarif" ? "number" : "text"}
                      />
                    </div>
                  ))}
                </div>
              ))}
              <button className="add-row" style={styles.addRowBtn} onClick={() => setMedLines(l => [...l, EMPTY_LINE()])}>
                + Ajouter une ligne
              </button>
              <div style={styles.totalRow}>
                <span style={{ color: COLORS.muted, fontSize: 13 }}>TOTAL TTC</span>
                <span style={{ fontWeight: 700, fontSize: 16, color: COLORS.accent }}>{sumMedical()} DH</span>
              </div>
            </div>
          </div>
        )}

        {/* FORMULAIRE 2 — CLINIQUE */}
        {partnerType === "clinique" && (
          <div className="fade-in">
            <div style={styles.formCard}>
              <div style={styles.sectionTitle}>🏨 Informations Clinique</div>
              <div style={{ ...styles.grid2, marginBottom: 14 }}>
                <div style={styles.field}>
                  <label style={styles.label}>Nom Clinique</label>
                  <input style={styles.input} value={clinInfo.nomClinique} onChange={e => setClinInfo({ ...clinInfo, nomClinique: e.target.value })} placeholder="Clinique Al Fath..." />
                </div>
                <div style={styles.field}>
                  <label style={styles.label}>ICE</label>
                  <input style={styles.input} value={clinInfo.ice} onChange={e => setClinInfo({ ...clinInfo, ice: e.target.value })} />
                </div>
                <div style={styles.field}>
                  <label style={styles.label}>N° Facture</label>
                  <input style={styles.input} value={clinInfo.numFacture} onChange={e => setClinInfo({ ...clinInfo, numFacture: e.target.value })} placeholder="FAC-2025-001" />
                </div>
                <div style={styles.field}>
                  <label style={styles.label}>Date Facture</label>
                  <input type="date" style={styles.input} value={clinInfo.dateFacture} onChange={e => setClinInfo({ ...clinInfo, dateFacture: e.target.value })} />
                </div>
                <div style={styles.field}>
                  <label style={styles.label}>Nom du Patient</label>
                  <input style={styles.input} value={clinInfo.nomPatient} onChange={e => setClinInfo({ ...clinInfo, nomPatient: e.target.value })} placeholder="Mohamed Alami" />
                </div>
                <div style={{ display: "flex", gap: 8 }}>
                  <div style={{ ...styles.field, flex: 1 }}>
                    <label style={styles.label}>Hospitalisation Du</label>
                    <input type="date" style={styles.input} value={clinInfo.hospitalisationDu} onChange={e => setClinInfo({ ...clinInfo, hospitalisationDu: e.target.value })} />
                  </div>
                  <div style={{ ...styles.field, flex: 1 }}>
                    <label style={styles.label}>Au</label>
                    <input type="date" style={styles.input} value={clinInfo.hospitalisationAu} onChange={e => setClinInfo({ ...clinInfo, hospitalisationAu: e.target.value })} />
                  </div>
                </div>
              </div>
            </div>

            {/* Prestations Clinique */}
            <div style={styles.formCard}>
              <div style={styles.sectionTitle}>🏥 Prestations Clinique</div>
              <div style={{ display: "grid", gridTemplateColumns: colsClinic.join(" "), background: "#252a42", borderRadius: "8px 8px 0 0", overflow: "hidden", marginBottom: 1 }}>
                {["Désignation", "Lettre Clé", "Nbre", "Prix Unitaire", "Montant DH"].map(h => (
                  <div key={h} style={styles.th}>{h}</div>
                ))}
              </div>
              {prestationsClinic.map(row => (
                <div key={row.id} style={{ display: "grid", gridTemplateColumns: colsClinic.join(" "), background: COLORS.border, marginBottom: 1 }}>
                  {["designation", "lettresCle", "nbre", "prixUnitaire", "montant"].map(f => (
                    <div key={f} style={styles.td}>
                      <input
                        style={styles.tdInput}
                        value={row[f]}
                        onChange={e => updatePrestation(setPrestationsClinic, row.id, f, e.target.value)}
                        placeholder={["montant", "prixUnitaire", "nbre"].includes(f) ? "0" : "—"}
                        type={["montant", "prixUnitaire", "nbre"].includes(f) ? "number" : "text"}
                      />
                    </div>
                  ))}
                </div>
              ))}
              <div style={styles.totalRow}>
                <span style={{ color: COLORS.muted, fontSize: 13 }}>TOTAL CLINIQUE</span>
                <span style={{ fontWeight: 700, color: COLORS.accent }}>{sumClinic()} DH</span>
              </div>
            </div>

            {/* Honoraires */}
            <div style={styles.formCard}>
              <div style={styles.sectionTitle}>👨‍⚕️ Honoraires & Autres Prestations</div>
              <div style={{ display: "grid", gridTemplateColumns: colsClinic.join(" "), background: "#252a42", borderRadius: "8px 8px 0 0", overflow: "hidden", marginBottom: 1 }}>
                {["Désignation", "Lettre Clé", "Nbre", "Prix Unitaire", "Montant DH"].map(h => (
                  <div key={h} style={styles.th}>{h}</div>
                ))}
              </div>
              {honoraires.map(row => (
                <div key={row.id} style={{ display: "grid", gridTemplateColumns: colsClinic.join(" "), background: COLORS.border, marginBottom: 1 }}>
                  {["designation", "lettresCle", "nbre", "prixUnitaire", "montant"].map(f => (
                    <div key={f} style={styles.td}>
                      <input
                        style={styles.tdInput}
                        value={row[f]}
                        onChange={e => updatePrestation(setHonoraires, row.id, f, e.target.value)}
                        placeholder={["montant", "prixUnitaire", "nbre"].includes(f) ? "0" : "—"}
                        type={["montant", "prixUnitaire", "nbre"].includes(f) ? "number" : "text"}
                      />
                    </div>
                  ))}
                </div>
              ))}
              <div style={styles.totalRow}>
                <span style={{ color: COLORS.muted, fontSize: 13 }}>TOTAL AUTRES PRESTATIONS</span>
                <span style={{ fontWeight: 700, color: COLORS.accent }}>{sumHonoraires()} DH</span>
              </div>
              <div style={{ ...styles.totalRow, borderTop: `2px solid ${COLORS.accent}`, paddingTop: 14 }}>
                <span style={{ fontWeight: 700, fontSize: 15 }}>TOTAL GÉNÉRAL</span>
                <span style={{ fontWeight: 800, fontSize: 20, color: COLORS.accent }}>
                  {(parseFloat(sumClinic()) + parseFloat(sumHonoraires())).toFixed(2)} DH
                </span>
              </div>
            </div>
          </div>
        )}

        {/* Generate Button */}
        <div style={{ display: "flex", gap: 12, marginTop: 8 }}>
          <button style={styles.btn} onClick={generate} disabled={loading}>
            {loading ? <div style={styles.spinner} /> : "✨"}
            {loading ? "Génération en cours..." : "Générer la Facture avec l'IA"}
          </button>
          {result && (
            <button style={styles.btnSecondary} onClick={() => { setResult(""); setStreamText(""); }}>
              Effacer
            </button>
          )}
        </div>

        {/* Result */}
        {(loading || result) && (
          <div style={styles.resultBox} className="fade-in">
            {result && <div style={styles.resultBadge}>✓ Généré</div>}
            <div style={{ ...styles.sectionTitle, marginBottom: 16 }}>
              🤖 Facturation générée par IA
            </div>
            {loading && !streamText && (
              <div style={styles.loader}>
                <div style={styles.spinner} />
                Analyse des données en cours...
              </div>
            )}
            <pre style={styles.resultText}>{streamText || result}</pre>
          </div>
        )}
      </div>
    </div>
  );
}
