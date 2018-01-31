<?php
/**
 * Created by PhpStorm.
 * User: ayata
 * Date: 30/01/18
 * Time: 14:26
 */

namespace School\GeralBundle\Command;

use School\AlunoBundle\Entity\Aluno;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Input\InputArgument;

class ImportAlunosCommand extends Command
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setName('school:import:students')
            ->addArgument('file', InputArgument::REQUIRED, 'The file to input')
            ->setDescription('Imports alunos from csv')
            ->setHelp('This command allows you to import alunos to the system');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Atempting to upload the students");

        $file = $input->getArgument('file');
        if (file_exists($file) && is_file($file) &&  pathinfo($file)['extension'] == 'csv') {

            $csv = Reader::createFromPath($file);
            $csv->setDelimiter(';');
            $csv->setHeaderOffset(0);

            $records = $csv->getRecords();

            foreach ($records as $key => $row) {
                $uid = $row['name'] . $row['id'];
                $username = str_replace(" ", "", $uid);

                $aluno = (new Aluno())
                    ->setName($row['name'])
                    ->setEnabled(true)
                    ->setPlainPassword("password")
                    ->setUsername($username)
                    ->setEmail($username . '@portabilis.com')
                    ->setCpf($row['cpf'])
                    ->setRg($row['rg'])
                    ->setTelefone($row['phone'])
                    ->setDataNascimento(new \DateTime($row['birthday']))
                    ->setIdImported($row['id']);
                $this->em->persist($aluno);
                if (($key % 25) === 0) {
                    $this->em->flush();
                    $this->em->clear(); // Detaches all objects from Doctrine!
                }
            }
            $this->em->flush(); //Persist objects that did not make up an entire batch
            $this->em->clear();
            $io->success('Students imported!');
        }
        else{
            $io->error("No such a csv file");
        }
    }
}