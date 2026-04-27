import os

path = r'd:\xampp\htdocs\fabou_la\database\seeders\DharmaGroupSeeder.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = "'玄應宮' => ['元續', '赤峰'],"
replacement = "'玄應宮' => ['元續', '赤峰'],\n            '副宮主' => ['靈妙', '赤峰', '閻澤', '金了'],"

content = content.replace(target, replacement)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)
