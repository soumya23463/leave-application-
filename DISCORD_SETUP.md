# Discord Integration — Setup Guide

This app can send **Discord DMs** for leave events (request submitted, approved, rejected,
new employee added). Each user connects their own Discord account once, and the bot then
DMs them automatically.

> **How it works:** Discord only lets a bot DM a user if they share a server. So the app
> uses the `guilds.join` scope to **auto-add** each user to your server when they click
> "Connect Discord". You only need to set things up once.

---

## What you need to fill in `.env`

```env
APP_URL=http://127.0.0.1:8000

DISCORD_CLIENT_ID=
DISCORD_CLIENT_SECRET=
DISCORD_BOT_TOKEN=
DISCORD_GUILD_ID=
DISCORD_REDIRECT_URI=http://127.0.0.1:8000/auth/discord/callback
```

The steps below tell you where each value comes from.

---

## Step 1 — Create a Discord Application
1. Go to <https://discord.com/developers/applications> (logged in with your Discord account).
2. Click **New Application** → give it a name (e.g. "Leave Bot") → **Create**.

## Step 2 — Get Client ID & Secret
1. Left menu → **OAuth2**.
2. Copy **CLIENT ID** → this is `DISCORD_CLIENT_ID`.
3. Click **Reset Secret** → copy it → this is `DISCORD_CLIENT_SECRET`.

## Step 3 — Add the Redirect URI ⚠️ (most important)
1. OAuth2 → **Redirects** → **Add Redirect**.
2. Enter the exact URL where the app runs, with `/auth/discord/callback`:
   - Local: `http://127.0.0.1:8000/auth/discord/callback`
   - Hosted: `https://your-domain.com/auth/discord/callback`
3. **Save Changes.**

> This must match `DISCORD_REDIRECT_URI` in `.env` **exactly**, otherwise you'll get
> "Failed to connect Discord".

## Step 4 — Create the Bot
1. Left menu → **Bot**.
2. Click **Reset Token** → copy it → this is `DISCORD_BOT_TOKEN`.
3. Under **Privileged Gateway Intents**, turn **SERVER MEMBERS INTENT** = **ON** → Save.

## Step 5 — Invite the Bot to your server
1. Build this URL (replace `YOUR_CLIENT_ID` with the value from Step 2):
   ```
   https://discord.com/oauth2/authorize?client_id=YOUR_CLIENT_ID&scope=bot&permissions=1
   ```
   - `permissions=1` = **Create Instant Invite** (required so the app can auto-add members).
   - Use `permissions=33` for Create Invite + Manage Server, or `permissions=8` for Administrator.
2. Open the URL → select **your server** → **Authorize**. The bot now joins your server.

## Step 6 — Get your Server (Guild) ID
1. Discord app → **User Settings → Advanced → Developer Mode = ON**.
2. Right-click your **server name → Copy Server ID** → this is `DISCORD_GUILD_ID`.

## Step 7 — Fill `.env`
Paste all the values collected above into `.env` (see the block near the top of this file).

## Step 8 — Clear cached config
```bash
php artisan config:clear
```

## Step 9 — Test it
1. Run the app: `php artisan serve` (serves at `http://127.0.0.1:8000`).
2. Log in → top-right user dropdown → **Connect Discord** → **Authorize**.
3. You should see "Discord account connected successfully!".
4. Submit / approve / reject a leave request — the bot will DM the relevant users.

---

## Notes
- **Each user** connects once via "Connect Discord"; the app auto-adds them to the server —
  they don't need to join manually.
- For DMs to arrive, the user's Discord privacy must allow
  **"Allow direct messages from server members"**.
- `.env` is **not** committed to git. Only `.env.example` (with blank keys) ships with the
  project, so each deployment must fill in its own five Discord values.

## Troubleshooting
| Problem | Likely cause / fix |
|---|---|
| "Failed to connect Discord" | Redirect URI in the Developer Portal ≠ `DISCORD_REDIRECT_URI` in `.env`. Make them identical. |
| Connected, but no DMs arrive | User's DM privacy is off, **or** the bot isn't in the server (`DISCORD_GUILD_ID`), **or** Server Members Intent is off. |
| Auto server-join fails | Bot lacks **Create Instant Invite** permission — re-invite with `permissions=1` (or higher). |
| Changed `.env` but nothing changed | Run `php artisan config:clear` (and `config:cache` in production). |

---

## Environment variable reference

| Key | Where it comes from |
|---|---|
| `DISCORD_CLIENT_ID` | OAuth2 → Client ID |
| `DISCORD_CLIENT_SECRET` | OAuth2 → Reset Secret |
| `DISCORD_BOT_TOKEN` | Bot → Reset Token |
| `DISCORD_GUILD_ID` | Right-click server → Copy Server ID (Developer Mode on) |
| `DISCORD_REDIRECT_URI` | Your app URL + `/auth/discord/callback` (must match a registered redirect) |
