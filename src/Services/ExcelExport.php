<?php

namespace LaravelEnso\Excel\Services;

use Box\Spout\Common\Entity\Row;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\XLSX\Writer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use LaravelEnso\Excel\Contracts\ExportsExcel;
use LaravelEnso\Excel\Contracts\SavesToDisk;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcelExport
{
    private Writer $writer;
    private ExportsExcel $exporter;

    public function __construct(ExportsExcel $exporter)
    {
        $this->exporter = $exporter;
    }

    public function inline(): BinaryFileResponse
    {
        $this->handle();

        return Response::download(
            $this->path(),
            $this->exporter->filename()
        )->deleteFileAfterSend();
    }

    public function save(): string
    {
        $this->handle();

        return $this->path();
    }

    private function handle(): void
    {
        $this->writer();

        Collection::wrap($this->exporter->sheets())
            ->each(fn ($sheet, $index) => $this
                ->sheet($sheet, $index)
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

        $this->writer->openToFile($this->path());
    }

    private function sheet(string $sheet, int $index): self
    {
        if ($index > 0) {
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

    private function path(): string
    {
        $folder = $this->exporter instanceof SavesToDisk
            ? $this->exporter->folder()
            : 'temp';

        if (! Storage::exists($folder)) {
            Storage::makeDirectory($folder);
        }

        return Storage::path("{$folder}/{$this->exporter->filename()}");
    }
}
