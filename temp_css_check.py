import re, os
root = r'c:\laragon\www\juventudiskali'
text_files = []
for dirpath, dirnames, filenames in os.walk(root):
    for fn in filenames:
        if fn.endswith(('.php', '.html', '.js')):
            text_files.append(os.path.join(dirpath, fn))
all_text = ''
for fn in text_files:
    with open(fn, 'r', encoding='utf-8', errors='ignore') as f:
        all_text += f.read() + '\n'
for css in ['public/css/Landing_Styles.css', 'public/css/ModulosAdmi.css', 'public/css/Login_Styles.css']:
    path = os.path.join(root, css)
    data = open(path, 'r', encoding='utf-8').read()
    braces = data.count('{') - data.count('}')
    selectors = [m.group(0).strip() for m in re.finditer(r'([^\{]+)\{', data)]
    simple = set()
    for sel in selectors:
        for part in sel.split(','):
            p = part.strip()
            if p.startswith('.') or p.startswith('#'):
                simple.add(p)
    unused = [s for s in sorted(simple) if not re.search(r'\b' + re.escape(s[1:]) + r'\b', all_text)]
    print(css, 'braces', braces, 'selectors', len(simple), 'unused', len(unused))
    print('first unused', unused[:20])
