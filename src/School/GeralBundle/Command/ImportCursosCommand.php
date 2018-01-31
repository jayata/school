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
        if (file_exists($file) && is_file($file) &&  pathinfo($file)['extension'] == 'csv') {

            $csv = Reader::createFromPath($file);
            $csv->setHeaderOffset(0);
            $csv->setDelimiter(',');
            $records = $csv->getRecords();

            //create courses
            foreach ($records as $key => $row) {
                $curso = (new Curso())
                    ->setNome($row['course_name'])
                    ->setMensualidade($row['monthly_amount'])
                    ->setValorMatricula($row['registration_tax'])
                    ->setPeriodo($row['period'])
                    ->setMesesDuracao(1)
                    ->setIdImported($row['id'])
                    ->setDescripcao("Imported via command");
                $this->em->persist($curso);
                if (($key % 25) === 0) {
                    $this->em->flush();
                    $this->em->clear(); // Detaches all objects from Doctrine!
                }
            }

            $this->em->flush(); //Persist objects that did not make up an entire batch
            $this->em->clear();

            //opens a matricula for each course
            //active but not payed
            $cursos = $this->em->getRepository('SchoolCursoBundle:Curso')->findAll();
            foreach ($cursos as $key => $curso1) {
                $matricula = (new Matricula())
                    ->setCurso($curso1)
                    ->setAtiva(true)
                    ->setAno(new \DateTime('now'));
                $this->em->persist($matricula);
                if (($key % 25) === 0) {
                    $this->em->flush();
                }
            }
            $this->em->flush(); //Persist objects that did not make up an entire batch
            $this->em->clear();
            $io->success('Cursos imported!');
        }else{
            $io->error("No such a csv file");
        }
    }
}