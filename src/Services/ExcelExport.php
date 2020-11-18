<?php

namespace LaravelEnso\Excel\Services;

use Box\Spout\Common\Entity\Row;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\XLSX\Writer;
use Illuminate\Support\Collection;
use LaravelEnso\Excel\Contracts\ExportsExcel;
use LaravelEnso\Excel\Contracts\SavesToDisk;
use LaravelEnso\Excel\Exceptions\ExcelExport as Exception;

class ExcelExport
{
    private Writer $writer;
    private ExportsExcel $exporter;
    private bool $inline;

    public function __construct(ExportsExcel $exporter)
    {
        $this->exporter = $exporter;
        $this->inline = true;
    }

    public function inline(): string
    {
        $this->handle();

        return $this->exporter->filename();
    }

    public function save(): string
    {
        if (! $this->exporter instanceof SavesToDisk) {
            throw Exception::missingInterface();
        }

        $this->inline = false;

        $this->handle();

        return $this->filePath();
    }

    private function handle(): void
    {
        $this->writer();

        Collection::wrap($this->exporter->sheets())
            ->each(fn ($sheet, $sheetIndex) => $this->sheet($sheet, $sheetIndex)
                ->heading($sheet)
                ->rows($sheet));

        $this->writer->close();
    }

    private function writer()
    {
        $defaultStyle = (new StyleBuilder())
            ->setShouldWrapText(false)
            ->build();

        $this->writer = WriterEntityFactory::createXLSXWriter();

        $this->writer->setDefaultRowStyle($defaultStyle);

        if ($this->inline) {
            $this->writer->openToBrowser($this->exporter->filename());

            return $this;
        }

        $this->writer->openToFile($this->filePath());
    }

    private function sheet(string $sheet, int $sheetIndex): self
    {
        if ($sheetIndex > 0) {
            $this->writer->addNewSheetAndMakeItCurrent();
        }

        $this->writer->getCurrentSheet()->setName($sheet);

        return $this;
    }

    private function heading(string $sheet): self
    {
        $this->writer->addRow($this->row($this->exporter->heading($sheet)));

        return $this;
    }

    private function rows(string $sheet): self
    {
        foreach ($this->exporter->rows($sheet) as $row) {
            $this->writer->addRow($this->row($row));
        }

        return $this;
    }

    private function row($data): Row
    {
        return WriterEntityFactory::createRowFromArray($data);
    }

    private function filePath(): string
    {
        return $this->exporter->path()
            .DIRECTORY_SEPARATOR
            .$this->exporter->filename();
    }
}
