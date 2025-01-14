<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Helpers;

use Jsvrcek\ICS\CalendarExport;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Utility\Formatter;
use Ramsey\Uuid\Uuid;
use TitleDK\Calendar\DateTime\DateTimeHelper;

class ICSExportHelper
{

    use DateTimeHelper;

    /** @var \TitleDK\Calendar\Helpers\Calendar The SS Calendar object */
    protected $ssCalendar;

    /** @var string The ICS output */
    protected $strics = '';

    /**
     * @param \TitleDK\Calendar\Calendars\Calendar $ssCalendar SilverStripe calendar
     * @throws \Jsvrcek\ICS\Exception\CalendarEventException
     */
    public function processCalendar(Calendar $ssCalendar): string
    {
        $this->ssCalendar = $ssCalendar;
        $this->strics = '';
        $icsCalendar = new \Jsvrcek\ICS\Model\Calendar();

        // @todo Fix this, config I guess
        $icsCalendar->setProdId('-//My Company//Cool CalNendar App//EN');

        // @todo Check ordering
        /** @var \TitleDK\Calendar\Events\Event $ssEvent */
        foreach ($ssCalendar->Events() as $ssEvent) {
            $icsEvent = new CalendarEvent();

            $startCarbon = $this->carbonDateTime($ssEvent->StartDateTime);
            $endCarbon = $this->carbonDateTime($ssEvent->EndDateTime);

            // this is the genuinely random UUID - base this on something else instead?
            // see https://packagist.org/packages/ramsey/uuid
            $uuid = Uuid::uuid4();

            $icsEvent->setStart($startCarbon->toDateTime())
                ->setEnd($endCarbon->toDateTime())
                ->setSummary($ssEvent->DetailsSummary())
                ->setAllDay($ssEvent->AllDay)
                ->setUid($uuid->toString())
                ->setStatus('CONFIRMED')
            ;

            $icsCalendar->addEvent($icsEvent);
        }

        $exporter = new CalendarExport(new CalendarStream(), new Formatter());
        $exporter->addCalendar($icsCalendar);

        $this->strics = $exporter->getStream();

        return $this->strics;
    }


    /**
     * getFile
     *
     * @param string $strFilename the names of the file
     * @param bool $headers True to return headers, false for testing
     */
    public function getFile(string $strFilename, bool $headers = true): void
    {
        \ob_start();
        if ($headers) {
            \header("Content-type: text/calendar");
            \header('Content-Disposition: attachment; filename="' . $strFilename . '"');
        }
        echo $this->strics;
        \ob_flush();
        die;
    }


    /**
     * getString
     *
     * @return string ics string
     */
    public function getString(): string
    {
        return $this->strics;
    }
}
