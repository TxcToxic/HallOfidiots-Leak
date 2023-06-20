import discord
import pymysql

client = discord.Client(intents=discord.Intents.all())

@client.event
async def on_ready():
    print(r"""
+=================================================================+
|    _    _       _ _          __   _____    _ _       _          |
|   | |  | |     | | |        / _| |_   _|  | (_)     | |         |
|   | |__| | __ _| | |   ___ | |_    | |  __| |_  ___ | |_ ___    |
|   |  __  |/ _` | | |  / _ \|  _|   | | / _` | |/ _ \| __/ __|   |
|   | |  | | (_| | | | | (_) | |    _| || (_| | | (_) | |_\__ \   |
|   |_|  |_|\__,_|_|_|  \___/|_|   |_____\__,_|_|\___/ \__|___/   |
+=================================================================+
""")


def is_not_pinned(mess):
    return not mess.pinned


@client.event
async def on_message(message):
    msg = message.content.casefold()
    if msg.startswith("-cd"):
        if message.author.guild_permissions.administrator:
            components = [discord.ActionRow(discord.Button(label="Create", custom_id="cap", emoji="📄"))]
            embed = discord.Embed(title="Create a Paste",
                                  description="Click the Button below to create a little Paste")
            await message.channel.send(embed=embed, components=components)
        await message.delete()

    if msg.startswith("-verify"):
        if message.author.guild_permissions.manage_roles:
            user = message.mentions[0]
            try:
                role = discord.utils.get(message.guild.roles, id=1106221345601237062)
                if role in user.roles:
                    await user.remove_roles(role)
                else:
                    await user.add_roles(role)
                await message.reply("Success!", delete_after=5)
            except Exception as e:
                print(e)
                await message.reply("An error occurred :/", delete_after=10)
        await message.delete()

    if msg.startswith("-clear"):
        if message.author.guild_permissions.manage_messages:
            args = message.content.split(' ')
            if len(args) == 2:
                if args[1].isdigit():
                    count = int(args[1]) + 1
                    deleted = await message.channel.purge(limit=count, check=is_not_pinned)
                    embed = discord.Embed(title="✅ | Messages deleted!",
                                          description="{} Messages have been deleted!".format(len(deleted) - 1))
                    await message.channel.send(embed=embed, delete_after=10)
                else:
                    embed = discord.Embed(title="Wrong Usage!",
                                          description="This command works with numbers")
                    await message.channel.send(embed=embed, delete_after=15)
                    await message.delete()
            else:
                embed = discord.Embed(title="Wrong Usage!",
                                      description="You have to add a Number!")
                await message.channel.send(embed=embed, delete_after=15)
                await message.delete()


@client.on_click(custom_id="cap")
async def on_click(i: discord.ComponentInteraction, button):
    if button.custom_id == "cap":
        role = discord.utils.get(i.guild.roles, id=1106221345601237062)
        if role not in i.member.roles:
            await i.respond(content="You're not able to use this Button!", hidden=True)
            return
        await i.respond_with_modal(modal=discord.Modal(custom_id='cap:sub', title="Create Paste on HoI",
                                                       components=[
                                      discord.TextInput(custom_id='name',
                                                        label='Name',
                                                        style=1,
                                                        min_length=1,
                                                        max_length=1000,
                                                        required=True,
                                                        placeholder='Max Mann'),
                                      discord.TextInput(custom_id='alias',
                                                        label="Alias",
                                                        min_length=1,
                                                        max_length=1000,
                                                        style=1,
                                                        required=False,
                                                        placeholder='DC: Max.Mann#1337'),
                                      discord.TextInput(custom_id='born',
                                                        label='Born',
                                                        min_length=1,
                                                        max_length=1000,
                                                        style=1,
                                                        required=False,
                                                        placeholder='10.10.1999 (XX y/o)'),
                                      discord.TextInput(custom_id='phone',
                                                        label='Phone',
                                                        min_length=1,
                                                        max_length=1000,
                                                        required=False,
                                                        placeholder='+1-505-1023'),
                                      discord.TextInput(custom_id='address',
                                                        label='Address',
                                                        min_length=1,
                                                        max_length=1000,
                                                        required=False,
                                                        placeholder='Lulstreet 187')]))


@client.on_submit('cap:sub')
@client.on_submit('cap:sub2')
async def on_submit(i: discord.ModalSubmitInteraction):
    if i.custom_id == "cap:sub":
        try:
            conn = pymysql.connect(
                host='localhost',
                user='hoi',
                password='e72rcjx7mnIkkpsAJKT2TJ8Ax5Vmnf2Y',
                database='hoi'
            )
            cursor = conn.cursor()

            name: str = i.get_field("name").value
            alias: str = i.get_field("alias").value
            born: str = i.get_field("born").value
            phone: str = i.get_field("phone").value
            address: str = i.get_field("address").value
            creator: str = f"{i.user.name}#{i.user.discriminator}"
            query = "INSERT INTO doxes (name, alias, born, phone, address, creator_name) VALUES (%s, %s, %s, %s, %s, %s)"
            values = (name, alias, born, phone, address, creator)

            cursor.execute(query, values)
            conn.commit()

            conn.close()

            await i.respond("Success!", hidden=True)
        except pymysql.err.Error as e:
            await i.respond("An error occurred (DB)!", hidden=True)
        except:
            await i.respond("An error occurred (unknown)!", hidden=True)


client.run("MTEwNjE3MDM2NjExMzYyODIwMw.GYCWTt.S83DcV-7RXkXjZxq0UUSV4SYX8_fBUVTzCwI7A")
