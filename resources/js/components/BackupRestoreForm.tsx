import { useRef, useState } from 'react';
import { router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    AlertDialog,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';

export function BackupRestoreForm() {
    const [open, setOpen] = useState(false);
    const [file, setFile] = useState<File | null>(null);
    const [error, setError] = useState('');
    const inputRef = useRef<HTMLInputElement>(null);

    function handleFileChange(e: React.ChangeEvent<HTMLInputElement>) {
        const selected = e.target.files?.[0] ?? null;
        setFile(selected);
        setError('');
    }

    function handleConfirm() {
        if (!file) {
            setError('Please select a backup file.');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);

        router.post('/backup/restore', formData, {
            onSuccess: () => {
                setOpen(false);
                setFile(null);
                if (inputRef.current) inputRef.current.value = '';
            },
            onError: (errors) => {
                if (errors.file) setError(errors.file);
            },
        });
    }

    return (
        <div className="space-y-4">
            <div className="rounded-xl border border-amber/30 bg-amber-lt/30 dark:bg-amber-dark-bg/30 p-6">
                <h3 className="text-base font-semibold text-amber-600 dark:text-amber-600-300 mb-1">Restore Backup</h3>
                <p className="text-sm text-muted-foreground mb-4">
                    Restore the database from a previously exported JSON backup. This will overwrite existing data.
                </p>
                <div className="flex items-center gap-3">
                    <Input
                        ref={inputRef}
                        type="file"
                        accept=".json"
                        onChange={handleFileChange}
                        className="max-w-xs"
                    />
                    <Button
                        type="button"
                        variant="outline"
                        disabled={!file}
                        onClick={() => {
                            setError('');
                            setOpen(true);
                        }}
                    >
                        Restore
                    </Button>
                </div>
            </div>

            <AlertDialog open={open} onOpenChange={setOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Restore backup?</AlertDialogTitle>
                        <AlertDialogDescription>
                            This will overwrite the current database with the contents of the selected backup file. This action cannot be undone.
                        </AlertDialogDescription>
                    </AlertDialogHeader>

                    {error && <p className="text-xs text-destructive">{error}</p>}

                    <AlertDialogFooter>
                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                        <Button
                            variant="destructive"
                            onClick={handleConfirm}
                        >
                            Restore
                        </Button>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </div>
    );
}
