<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance;

use DateTimeImmutable;
use Omatech\MageCore\Editora\Domain\Instance\Exceptions\InvalidEndDatePublishingException;

final class Publication
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';
    private string $status = PublicationStatus::PENDING;
    private ?DateTimeImmutable $startPublishingDate = null;
    private ?DateTimeImmutable $endPublishingDate = null;

    public function fill(array $publication): void
    {
        assert(isset($publication['startPublishingDate']));
        $this->status = $publication['status'] ?? PublicationStatus::PENDING;
        $this->startPublishingDate = new DateTimeImmutable($publication['startPublishingDate']);
        if (isset($publication['endPublishingDate'])) {
            $this->endPublishingDate = new DateTimeImmutable($publication['endPublishingDate']);
        }
        $this->validateEndPublishingDate();
    }

    private function validateEndPublishingDate(): void
    {
        if ($this->endPublishingDate?->diff($this->startPublishingDate)->invert === 0) {
            InvalidEndDatePublishingException::withDate(
                $this->endPublishingDate->format(self::DATE_FORMAT),
                $this->startPublishingDate->format(self::DATE_FORMAT)
            );
        }
    }

    public function data(): array
    {
        return [
            'status' => $this->status,
            'startPublishingDate' => $this->startPublishingDate,
            'endPublishingDate' => $this->endPublishingDate,
        ];
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'startPublishingDate' => $this->startPublishingDate?->format(self::DATE_FORMAT),
            'endPublishingDate' => $this->endPublishingDate?->format(self::DATE_FORMAT),
        ];
    }
}
