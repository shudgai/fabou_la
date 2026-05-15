
$path = "resources/js/components/TeachingManager.vue"
# Try to read as Big5 (950)
$enc = [System.Text.Encoding]::GetEncoding(950)
$content = [System.IO.File]::ReadAllLines($path, $enc)

$newContent = New-Object System.Collections.Generic.List[string]

for ($i = 0; $i -lt $content.Count; $i++) {
    $line = $content[$i]
    if ($line -like "*{{ reorderMode ? '確認排序' : '修改*") {
        # Fix the line and restore the structure
        $newContent.Add("                            {{ reorderMode ? '確認排序' : '修改排序' }}")
        $newContent.Add("                        </button>")
        $newContent.Add("                    </div>")
        $newContent.Add("                </div>")
        $newContent.Add("")
        $newContent.Add("                <template v-if='currentCategory === null && !currentFolder && !addMode'>")
        $newContent.Add("                    <div class='flex-1 overflow-y-auto bg-slate-50 relative custom-scrollbar -webkit-overflow-scrolling-touch' style='height: calc(100dvh - 120px);'>")
        $newContent.Add("                        <div class='p-4 grid grid-cols-1 sm:grid-cols-2 gap-4'>")
        # Keep the comment part if it was on the same line
        if ($line -like "*<!-- Category 1*") {
             $newContent.Add("                            <!-- Category 1: Daily Teaching (Large Folder Style) -->")
        }
    } else {
        $newContent.Add($line)
    }
}

# Write back as UTF8
[System.IO.File]::WriteAllLines($path, $newContent, [System.Text.Encoding]::UTF8)
