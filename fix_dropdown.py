with open('resources/js/components/TeachingAddForm.vue', 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Find line 150 (0-indexed: 149) which should be the closing </div> of the teleported dropdown
# Insert </teleport> after it
target_line = 149  # 0-indexed line 150

if '</div>' in lines[target_line] and '</div>' in lines[target_line+1]:
    # Insert </teleport> after line 150
    teleport_close = '                                </teleport>\r\n'
    lines.insert(target_line + 1, teleport_close)
    with open('resources/js/components/TeachingAddForm.vue', 'w', encoding='utf-8') as f:
        f.writelines(lines)
    print('SUCCESS - inserted </teleport> after line 150')
    print('Lines 148-155:')
    for i, l in enumerate(lines[147:155], start=148):
        print(f'{i}: {repr(l[:80])}')
else:
    print('NOT MATCHED')
    print(repr(lines[target_line]))
    print(repr(lines[target_line+1]))
