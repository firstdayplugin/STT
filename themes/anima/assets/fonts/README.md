# Anima fonts (self-hosted for CSP-A)

Google Fonts `<link>` was removed (no external font host → stronger CSP + offline + privacy).
Drop these WOFF2 files here (exact names), then fonts.css picks them up automatically:

- `Inter-Regular.woff2` (400)
- `Inter-Medium.woff2` (500)
- `Inter-SemiBold.woff2` (600)
- `Inter-Bold.woff2` (700)
- `SpaceGrotesk-Medium.woff2` (500)
- `SpaceGrotesk-SemiBold.woff2` (600)
- `SpaceGrotesk-Bold.woff2` (700)

Sources: Inter (github.com/rsms/inter, OFL) · Space Grotesk (github.com/floriankarsten/space-grotesk, OFL).
Until added, the fallback stack (system-ui / Arial) renders — layout is unaffected (`font-display:swap`).
This session has no outbound network, so the files are added via Google Drive upload or at deploy.
