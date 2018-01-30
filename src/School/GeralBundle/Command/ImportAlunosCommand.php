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
            ->setDescription('Imports alunos from csv')
            ->setHelp('This command allows you to import alunos to the system')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $io = new SymfonyStyle($input, $output);
      $io-> title("Atempting to upload the students");

        $csv = Reader::createFromPath('/path/to/your/csv/file.csv', 'r');
        $csv->setDelimiter(';');
        $records = $csv->getRecords();

//        foreach ($records as $row) {
//            $aluno = (new Aluno())
//                ->setName($row['name'])
//                ->setUsername($row['name'])
//                ->setEmail($row['name'].'@portabilis.com')
//                ->setCpf($row['cpf'])
//                ->setRg($row['rg'])
//                ->setTelefone($row['phone'])
//
//                ->setDataNascimento(new \DateTime($row['birthday']))
//                ;
//        }
//        $this->em->persist($aluno);
//        $this->em->flush();
        $io->success('Students imported!');
    }
}