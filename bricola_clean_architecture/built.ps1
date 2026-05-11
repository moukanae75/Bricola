Remove-Item -Recurse -Force .\bin\* -ErrorAction SilentlyContinue
$files = Get-ChildItem -Recurse -Filter *.java -Path .\src | ForEach-Object { $_.FullName }
javac -d .\bin $files
java -cp .\bin Presentation.Main