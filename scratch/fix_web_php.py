import os

path = r'd:\xampp\htdocs\fabou_la\routes\web.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("with('dharmaNames:id,name')", "with('dharmaNames:id,name,order')")

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)
