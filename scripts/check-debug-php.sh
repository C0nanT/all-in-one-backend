#!/usr/bin/env bash
found=0
for file in "$@"; do
    if grep -n -E '\b(dd|dump|ray|var_dump)\s*\(' "$file" 2>/dev/null; then
        found=1
    fi
done
if [ "$found" -eq 1 ]; then
    echo "Erro: remova chamadas de debug (dd, dump, ray, var_dump) antes de commitar."
    exit 1
fi
exit 0
