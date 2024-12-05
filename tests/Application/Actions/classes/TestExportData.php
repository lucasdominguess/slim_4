<?php
namespace Tests\Application\classes;
use Exception;
use App\classes\ExportData;
use PHPUnit\Framework\TestCase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TestExportData extends TestCase
{
    private $exportData;

    public function setUp(): void
    {
        $this->exportData = new ExportData();
    }

    public function testEmptyArrayThrowsException()
    {
        $this->expectException(Exception::class);
        $this->exportData->excel([], '');
    }

    public function testSingleRowDataGeneratesValidExcelFile()
    {
        $dados = [['Name' => 'John', 'Age' => 30]];
        $title = 'Test';
        $result = $this->exportData->excel($dados, $title);
        $this->assertFileExists($result[0]);
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $result[1]);
    }

    public function testMultipleRowsDataGeneratesValidExcelFile()
    {
        $dados = [
            ['Name' => 'John', 'Age' => 30],
            ['Name' => 'Jane', 'Age' => 25],
            ['Name' => 'Bob', 'Age' => 40]
        ];
        $title = 'Test';
        $result = $this->exportData->excel($dados, $title);
        $this->assertFileExists($result[0]);
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $result[1]);
    }

    public function testErrorDuringFileGenerationThrowsException()
    {
        $this->expectException(Exception::class);
        $dados = [['Name' => 'John', 'Age' => 30]];
        $title = 'Test';
        // Simulate an error during file generation
        $writer = new Xlsx(new Spreadsheet());
        $writer->save('invalid_path');
        $this->exportData->excel($dados, $title);
    }

    public function testErrorLogging()
    {
        $this->expectException(Exception::class);
        $dados = [['Name' => 'John', 'Age' => 30]];
        $title = 'Test';
        // Simulate an error during file generation
        $writer = new Xlsx(new Spreadsheet());
        $writer->save('invalid_path');
        $this->exportData->excel($dados, $title);
        // Check if the error was logged correctly
        $logFile = 'logs/LOG_' . date('d-m-Y') . '.csv';
        $this->assertFileExists($logFile);
    }
}