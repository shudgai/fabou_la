import os

path = r'd:\xampp\htdocs\fabou_la\database\seeders\DharmaGroupSeeder.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Ensure the group exists with ID 40
creation_logic = """        // Ensure '全體殿生' exists with ID 40
        Group::updateOrCreate(['id' => 40], ['name' => '全體殿生']);
        $groupCache = Group::all()->keyBy('name');"""

if "Ensure '全體殿生' exists" not in content:
    content = content.replace("$groupCache = Group::all()->keyBy('name');", creation_logic)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Seeder updated to ensure '全體殿生' group existence.")
