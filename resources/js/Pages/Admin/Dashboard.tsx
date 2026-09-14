import { Head } from '@inertiajs/react';

export default function Dashboard() {
    return (
        <>
            <Head title="WA Bridge Admin" />
            <div className="min-h-screen bg-gray-50 p-8">
                <h1 className="text-2xl font-semibold text-gray-900">
                    WA Bridge — Platform Admin
                </h1>
                <p className="mt-2 text-gray-600">
                    Foundation stage placeholder. Organizations, subscriptions,
                    payments, and credits panels are added in the Commercial
                    foundation stage.
                </p>
            </div>
        </>
    );
}
