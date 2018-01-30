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
            ->setDescription('Imports courses from csv')
            ->setHelp('This command allows you to import courses to the system')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $io = new SymfonyStyle($input, $output);
      $io-> title("Atempting to upload the courses");

        $csv = Reader::createFromPath('%kernel.root_dir%/../src/School/GeralBundle/Data/courses_file.csv');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(',');
        $records = $csv->getRecords();

        foreach ($records as $row) {
            $curso = (new Curso())
                ->setNome($row['course_name'])
                ->setMensualidade($row['monthly_amount'])
                ->setValorMatricula($row['registration_tax'])
                ->setPeriodo($row['period'])
                ->setMesesDuracao(1)
                ->setIdImported($row['id'])
                ->setDescripcao("Imported via command")
                ;
            $this->em->persist($curso);
        }

        $this->em->flush();

        $cursos = $this->em->getRepository('SchoolCursoBundle:Curso')->findAll();
        foreach ($cursos as $curso){
            $matricula = (new Matricula())
                ->setCurso($curso)
                ->setAtiva(true)
                ->setAno(new \DateTime('now'))
            ;
            $this->em->persist($matricula);
        }
        $this->em->flush();
        $io->success('Cursos imported!');
    }
}