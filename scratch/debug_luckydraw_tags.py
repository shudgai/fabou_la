import os
import re

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

template_match = re.search(r'<template>(.*?)</template>', content, re.DOTALL)
if template_match:
    template = template_match.group(1)
    
    # Simple tag counter
    stack = []
    lines = template.split('\n')
    for i, line in enumerate(lines):
        # Find all <div or </div
        tags = re.findall(r'<(/?div|/?template|/?section|/?button|/?span|/?mobile-navbar)', line)
        for tag in tags:
            if tag.startswith('/'):
                if not stack:
                    print(f"Extra closing tag </{tag[1:]}> on line {i+1}")
                else:
                    stack.pop()
            else:
                stack.append((tag, i+1))
    
    if stack:
        print("Unclosed tags:")
        for tag, line_num in stack:
            print(f"<{tag}> on line {line_num}")

# Manual Fix based on previous Turn 94 observation
# Line 131 is an extra div if 157 is also there.
# I'll check line 157.
