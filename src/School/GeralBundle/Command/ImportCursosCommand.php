<?php
/**
 * Created by PhpStorm.
 * User: ayata
 * Date: 30/01/18
 * Time: 14:26
 */

namespace School\GeralBundle\Command;

use School\CursoBundle\Entity\Curso;
use School\MatriculaBundle\Entity\Matricula;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\ProgressBar;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class ImportCursosCommand extends Command
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setName('school:import:courses')
            ->addArgument('file', InputArgument::REQUIRED, 'The file to input')
            ->setDescription('Imports courses from csv')
            ->setHelp('This command allows you to import courses to the system');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Atempting to upload the courses");

        $file = $input->getArgument('file');
        if (file_exists($file) && is_file($file) && pathinfo($file)['extension'] == 'csv') {

            $csv = Reader::createFromPath($file);
            $csv->setHeaderOffset(0);
            $csv->setDelimiter(',');
            $count = $csv->count();
            $records = $csv->getRecords();

            $progress = new ProgressBar($output, $count);
            $progress->start();
            foreach ($records as $key => $row) {
                try {
                    $curso = (new Curso())
                        ->setNome($row['course_name'])
                        ->setMensualidade($row['monthly_amount'])
                        ->setValorMatricula($row['registration_tax'])
                        ->setPeriodo($row['period'])
                        ->setMesesDuracao(1)
                        ->setIdImported($row['id'])
                        ->setDescripcao("Imported via command");
                    $this->em->persist($curso);
                    $this->em->flush();
                } catch (UniqueConstraintViolationException $exception) {
                    $this->checkEm();
                    continue;
                }
                try {
                    $matricula = (new Matricula())
                        ->setCurso($curso)
                        ->setAtiva(true)
                        ->setAno(new \DateTime('now'));
                    $this->em->persist($matricula);
                    $this->em->flush();
                } catch (UniqueConstraintViolationException $exception) {
                    $this->checkEm();
                    continue;
                }
                $progress->advance();
            }
            $progress->finish();
            $io->newLine();
            $io->success('Cursos imported!');
        } else {
            $io->error("No such a csv file");
        }
    }

    public function checkEm()
    {
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(), $this->em->getConfiguration());
        }
    }
}