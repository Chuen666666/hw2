import os
import re
import subprocess
from pathlib import Path
from typing import Any, cast

import discord
import mysql.connector
from discord import app_commands
from discord.ext import commands
from dotenv import load_dotenv


BASE_DIR = Path(__file__).resolve().parent
CONFIG_PATH = BASE_DIR.parent / 'config.php'
ENV_PATH = BASE_DIR / '.env'

load_dotenv(ENV_PATH, override=True)
TOKEN = os.getenv('DISCORD_TOKEN')


result = subprocess.run(
    ['php', '-r', f'require "{CONFIG_PATH}"; echo $registration_table;'],
    capture_output=True,
    text=True,
    check=True,
)

REGISTRATION_TABLE = result.stdout.strip()

if not re.fullmatch(r'[A-Za-z0-9_]+', REGISTRATION_TABLE):
    raise ValueError('Invalid registration table name')

DB_HOST = os.getenv('DB_HOST')
DB_NAME = os.getenv('DB_NAME')
DB_USER = os.getenv('DB_USER')
DB_PASSWORD = os.getenv('DB_PASSWORD')

intents = discord.Intents.default()
bot = commands.Bot(command_prefix='!', intents=intents)


@bot.event
async def on_ready():
    print(f'Logged in as {bot.user}')


@bot.tree.command(name='check', description='查詢奶龍選美大賽報名狀態')
@app_commands.describe(query='請輸入學號或名字')
@app_commands.allowed_installs(guilds=True, users=True)
@app_commands.allowed_contexts(guilds=True, dms=True, private_channels=True)
async def check(interaction: discord.Interaction, query: str):
    try:
        connection = mysql.connector.connect(
            host=DB_HOST, database=DB_NAME, user=DB_USER, password=DB_PASSWORD
        )

        cursor = connection.cursor(dictionary=True, buffered=True)

        cursor.execute(
            f"""
            SELECT name, student_id, grade, category
            FROM `{REGISTRATION_TABLE}`
            WHERE student_id = %s OR name = %s
            """,
            (query, query),
        )

        rows = cast(list[dict[str, Any]], cursor.fetchall())

        cursor.close()
        connection.close()

        if not rows:
            await interaction.response.send_message(f'查無 {query} 的報名資料。')
            return

        if len(rows) > 1:
            student_ids = '\n'.join(f'- {row["student_id"]}' for row in rows)

            await interaction.response.send_message(
                f'找到多筆姓名為「{query}」的報名資料：\n{student_ids}\n請改用學號查詢。'
            )
            return

        registration = rows[0]
        await interaction.response.send_message(
            f'已完成報名\n'
            f'姓名：{registration["name"]}\n'
            f'學號：{registration["student_id"]}\n'
            f'年級：大{registration["grade"]}\n'
            f'報名組別：{registration["category"]}'
        )

    except mysql.connector.Error as e:
        print(f'Database error: {e}')

        await interaction.response.send_message('資料庫連線發生錯誤。', ephemeral=True)


@bot.event
async def setup_hook():
    await bot.tree.sync()


if __name__ == '__main__' and TOKEN:
    bot.run(TOKEN)
