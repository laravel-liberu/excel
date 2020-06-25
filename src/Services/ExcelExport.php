<?php

namespace LaravelEnso\Excel\Services;

use Box\Spout\Common\Entity\Row;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\XLSX\Writer;
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
        $this->writer()
            ->heading()
            ->rows();

        $this->writer->close();
    }

    private function writer(): self
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

        return $this;
    }

    private function heading(): self
    {
        $this->writer->addRow($this->row($this->exporter->heading()));

        return $this;
    }

    private function rows(): self
    {
        foreach ($this->exporter->rows() as $row) {
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
