<style>
label {
    width: 120px;
    float: left;
    text-align: right;
    margin-right: 5px;
    display: block
}

#subbut {
    margin-top: 10px;
    margin-left: 124px;   
}

textarea {
    width: 380px;
    height: 140px;
}

</style>

<h1>Mass Upload</h1>
<p>Copy a bunch of quotes into the textarea below.  Make sure that each quote is on its own line.</p>
<p>Give the quotefile a name, and let it rip. The quotes will be put into a moderation queue for you to approve, although they will be visible to the public with a "Pending Approval" sticker on their page.</p>

<hr>

<?= $this->Form->create('Users', array('action' => 'mass_upload', 'method' => 'post')); ?>
<?= $this->Form->input('name', array('label' => 'quotefile name')); ?>
<label for="quote">quotefile</label><?= $this->Form->textarea('quotefile'); ?>
<?= $this->Form->submit('mass upload quotefile', array('id' => 'subbut')); ?>