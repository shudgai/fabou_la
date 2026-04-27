import os

path = r'd:\xampp\htdocs\fabou_la\database\seeders\DharmaGroupSeeder.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add '全體殿生' to the seeder
# I'll add logic to fetch all dharma names in the seeder
# Or just use the truthMatrix logic

if "'全體殿生'" not in content:
    # Insert before the end of the array
    content = content.replace("'太子直屬弟子' => ['龍勝', '龍戰'],", "'太子直屬弟子' => ['龍勝', '龍戰'],\n            '全體殿生' => [],")

# Update the seeder logic to populate '全體殿生' if it's empty
population_logic = """        // Handle '全體殿生' specially if it's in the list
        if (isset($groupMembers['全體殿生'])) {
            $groupMembers['全體殿生'] = DharmaName::pluck('name')->toArray();
        }"""

if "Handle '全體殿生' specially" not in content:
    content = content.replace("// Process Person-by-Person Truth", population_logic + "\n\n        // Process Person-by-Person Truth")

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Seeder updated with '全體殿生' group logic.")
