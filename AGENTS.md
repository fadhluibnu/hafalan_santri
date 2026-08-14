# AGENTS — Sistem Hafalan Santri (project aktif)

Ini adalah sistem yang benar-benar dipakai dan dijalankan. Repo git berakar di
folder ini, sedangkan rules workspace berada satu tingkat di atas repo, jadi baca
secara eksplisit:

- [00-project-context.md](../../.agents/rules/00-project-context.md)
- [10-workflow-skill-gating.md](../../.agents/rules/10-workflow-skill-gating.md)
- [20-environment-wsl.md](../../.agents/rules/20-environment-wsl.md)
- [30-conventions-laravel-inertia.md](../../.agents/rules/30-conventions-laravel-inertia.md)

Runbook on-demand: skill `hafalan-dev-workflow`
(`e:\KULIAH\HAFALAN_SANTRI\.agents\skills\hafalan-dev-workflow\SKILL.md`).

Inti yang tidak boleh dilanggar:

- Toolchain ada di **WSL**. Perintah npm/node wajib diawali
  `. /home/fadhluibnu/.nvm/nvm.sh`; path `NEW SISTEM` wajib di-quote; jangan pakai
  `$(...)` di dalam perintah `wsl` (ditelan PowerShell).
- Permintaan non-trivial → fase PLAN dulu (`interview-me` → `idea-refine` →
  `spec-driven-development` → `planning-and-task-breakdown`), tunggu persetujuan USER.
- Halaman Inertia baru harus `.jsx`; `resources/js/{actions,routes,wayfinder}`
  adalah file generated Wayfinder — jangan diedit manual.
- Scoping pondok lewat trait `ResolvesPondokScope`; jangan percaya `pondok_id` dari
  request untuk peran selain `super_admin`.
- Baseline `npm run types` sudah merah (8 error di file generated + `ssr.tsx`).
  Bandingkan dengan baseline, jangan salah sebut regresi.
