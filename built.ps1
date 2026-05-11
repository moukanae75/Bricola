Remove-Item -Recurse -Force .\bin\* -ErrorAction SilentlyContinue

$files = Get-ChildItem -Recurse -Filter *.java -Path .\src | ForEach-Object { $_.FullName }

javac -d .\bin -cp ".\src\infrastructure\Config\mysql-connector-j-9.1.0.jar" $files

java -cp ".\bin;.\src\infrastructure\Config\mysql-connector-j-9.1.0.jar" Presentation.Main