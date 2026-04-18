
import re

file_path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Strip script and style
template_content = re.search(r'<template>(.*)</template>', content, re.DOTALL).group(1)

# Basic tag counter
def count_tags(v, tag):
    opens = len(re.findall(f'<{tag}[^>]*[^/]>', v))
    closes = len(re.findall(f'</{tag}>', v))
    return opens, closes

for tag in ['div', 'template', 'span', 'button', 'input', 'label', 'textarea']:
    o, c = count_tags(template_content, tag)
    print(f"{tag}: {o} opens, {c} closes. Balance: {o-c}")
