<?php

namespace CopeX\AgreementsPdf\Plugin;

use Fooman\EmailAttachments\Model\Api\AttachmentContainerInterface;
use Fooman\EmailAttachments\Model\ContentAttacher;
use Fooman\EmailAttachments\Model\TermsAndConditionsAttacher;
use Magento\CheckoutAgreements\Api\Data\AgreementInterface;

class TermsAndConfitions
{

    private ContentAttacher $contentAttacher;

    public function __construct(ContentAttacher $contentAttacher)
    {
        $this->contentAttacher = $contentAttacher;
    }

    /**
     * @param TermsAndConditionsAttacher $subject
     * @param callable                   $proceed
     * @param AgreementInterface         $agreement
     * @param AttachmentContainerInterface         $attachmentContainer
     * @return void
     */
    public function aroundAttachAgreement(
        TermsAndConditionsAttacher $subject,
        callable $proceed,
        AgreementInterface $agreement,
        AttachmentContainerInterface $attachmentContainer
    ): void {
        $this->contentAttacher->addPdf(
            $this->buildPdfAgreement($agreement),
            $agreement->getName() . '.pdf',
            $attachmentContainer
        );
    }

    private function buildPdfAgreement(AgreementInterface $agreement)
    {
        $mpdf = new \Mpdf\Mpdf();
        $content = $agreement->getContent();
        if (!$agreement->getIsHtml()) {
            $content = nl2br($content);
        }
        $mpdf->WriteHTML($content);
        return $mpdf->Output('','S');
    }
}