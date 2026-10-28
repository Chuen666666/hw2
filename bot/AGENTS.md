# AGENTS.md

## Scope

These instructions apply to the `bot/` directory and supplement the repository-level `AGENTS.md`. Follow both files; this file takes precedence for Discord Bot work when the instructions differ.

## Application boundary

The Discord Bot is a separate Python application that shares the website's MySQL registration table. It does not call the PHP application directly.

Primary files:

```text
bot.py
requirements.txt
```

Runtime-only paths such as `.venv/`, `__pycache__/`, and log files must remain outside version control.

## Configuration and secrets

- Load configuration from the repository-level `../.env`.
- Never hard-code, print, log, or commit `DISCORD_TOKEN`, database credentials, or other secrets.
- Keep `example.env` in the repository root secret-free when configuration requirements change.
- Validate `DB_REGISTRATION_TABLE` as an identifier before interpolating it into SQL. SQL values must continue using connector parameters such as `%s`; never concatenate user input into SQL.

## Registration data and Discord responses

- Treat names, student IDs, grades, categories, email addresses, phone numbers, and PDF links as personal or sensitive registration data.
- Do not add more personal fields to Discord responses or broaden where responses are visible without explicit user approval.
- The current `/check` command can run in guilds, DMs, and private channels. Consider the response visibility and whether `ephemeral=True` is appropriate whenever command behavior changes.
- Do not expose database exceptions to Discord users. Diagnostic output must not contain credentials or tokens.
- If database field names change, update the PHP application, Bot query, schema, and project documentation together.

## Python and Discord conventions

- Keep compatibility with the production Python 3 environment and the versions permitted by `requirements.txt`.
- Use `discord.py` application commands consistently with the existing Bot structure.
- Avoid adding long blocking work inside async command handlers. If a new operation can block materially, isolate it appropriately.
- Ensure cursors and database connections are closed on both success and failure paths when changing database access.
- Do not create or commit a virtual environment. Install dependencies into `bot/.venv/` for local or production setup.

## Verification

For Bot-only changes, run at least:

```bash
python3 -m py_compile bot/bot.py
```

Where the environment is available, also verify Bot startup, slash-command synchronization, DM behavior, guild behavior, no-result queries, duplicate-name queries, exact student-ID queries, database failures, and that no sensitive values appear in logs.

Do not start or connect the production Bot merely to perform a syntax check.
