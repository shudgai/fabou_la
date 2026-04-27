import os

path = r'd:\xampp\htdocs\fabou_la\database\seeders\DharmaGroupSeeder.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# New definitions for palace groups based on the provided table
new_palace_groups = """            '玄通宮' => ['靈果', '靈妙', '靈智', '靈慧'],
            '玄應宮' => ['元續', '赤峰', '閻闇', '金悟'],
            '玄心宮' => ['龍戰', '龍勝', '金熹'],
            '玄妙宮' => ['金頤'],
            '玄昇宮' => ['靈心', '閻澤', '閻爵', '金護'],
            '玄願宮' => ['金振', '金淑'],
            '玄法宮' => ['靈昡'],
            '玄閻宮' => ['閻尊', '金了'],
            '玄窕宮' => ['金曉', '金諦', '金彩'],
            '玄瑤宮' => ['道妙'],
            '玄義宮' => ['閻願'],"""

# Also update the numbered groups (第一組 etc) to match
numbered_groups = """            '第一組' => ['靈果', '靈妙', '靈智', '靈慧'],
            '第二組' => ['元續', '赤峰', '閻闇', '金悟'],
            '第三組' => ['龍戰', '龍勝', '金熹'],
            '第四組' => ['金頤'],
            '第五組' => ['靈心', '閻澤', '閻爵', '金護'],
            '第六組' => ['金振', '金淑'],
            '第七組' => ['靈昡'],
            '第八組' => ['閻尊', '金了'],
            '第九組' => ['金曉', '金諦', '金彩'],
            '第十組' => ['道妙'],
            '第十一組' => ['閻願'],"""

# Replace the old lines
import re
content = re.sub(r"'玄通宮' => \[.*?\],", new_palace_groups, content, flags=re.DOTALL)
content = re.sub(r"'玄應宮' => \[.*?\],", "", content)
content = re.sub(r"'玄心宮' => \[.*?\],", "", content)
content = re.sub(r"'玄妙宮' => \[.*?\],", "", content)
content = re.sub(r"'玄昇宮' => \[.*?\],", "", content)
content = re.sub(r"'玄願宮' => \[.*?\],", "", content)
content = re.sub(r"'玄法宮' => \[.*?\],", "", content)
content = re.sub(r"'玄閻宮' => \[.*?\],", "", content)
content = re.sub(r"'玄窕宮' => \[.*?\],", "", content)
content = re.sub(r"'玄瑤宮' => \[.*?\],", "", content)
content = re.sub(r"'玄義宮' => \[.*?\],", "", content)

# Also add/update numbered groups if they are missing or old
if "'第一組'" in content:
    content = re.sub(r"'第一組' => \[.*?\],", numbered_groups, content, flags=re.DOTALL)
    # Remove others
    for i in ['二','三','四','五','六','七','八','九','十','十一']:
        content = re.sub(r"'第"+i+"組' => \[.*?\],", "", content)
else:
    # Insert before '殿中之人'
    content = content.replace("'殿中之人'", numbered_groups + "\n            '殿中之人'")

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Seeder updated with full palace memberships.")
