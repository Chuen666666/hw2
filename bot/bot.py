import os
from pathlib import Path
from typing import Any, cast

import discord
import mysql.connector
from discord import app_commands
from discord.ext import commands
from dotenv import load_dotenv


BASE_DIR = Path(__file__).resolve().parent
ENV_PATH = BASE_DIR / '.env'

load_dotenv(ENV_PATH, override=True)

TOKEN = os.getenv('DISCORD_TOKEN')

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

        cursor = connection.cursor(dictionary=True)

        cursor.execute(
            """
            SELECT name, student_id, grade, category
            FROM registrations
            WHERE student_id = %s OR name = %s
            """,
            (query, query),
        )

        row = cursor.fetchone()

        if row is None:
            await interaction.response.send_message(f'查無 {query} 的報名資料。')
            return

        registration = cast(dict[str, Any], row)

        cursor.close()
        connection.close()

        if registration is None:
            await interaction.response.send_message(f'查無 {query} 的報名資料。')
            return

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
