import os

path = r'd:\xampp\htdocs\fabou_la\database\seeders\DharmaGroupSeeder.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Rename 虎賁軍 to 虎寅軍
content = content.replace("'虎賁軍' => ['閻帝', '閻願'],", "'虎寅軍' => ['閻帝', '閻願'],")

# 2. Correct 元帥副元帥
content = content.replace("'元帥副元帥' => ['閻帝', '龍勝', '龍戰', '閻尊', '閻爵', '閻澤', '閻闇', '閻願'],", "'元帥副元帥' => ['閻帝', '閻願', '閻爵', '閻澤', '閻尊', '閻闇'],")

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)
