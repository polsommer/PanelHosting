import tw from 'twin.macro';
import { ApplicationStore } from '@/state';
import Can from '@/components/elements/Can';
import { httpErrorToHuman } from '@/api/http';
import { ServerContext } from '@/state/server';
import React, { useEffect, useState } from 'react';
import Spinner from '@/components/elements/Spinner';
import UserRow from '@/components/server/users/UserRow';
import { Actions, useStoreActions, useStoreState } from 'easy-peasy';
import getServerSubusers from '@/api/server/users/getServerSubusers';
import AddSubuserButton from '@/components/server/users/AddSubuserButton';
import ServerContentBlock from '@/components/elements/ServerContentBlock';

export default () => {
    const [loading, setLoading] = useState(true);

    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);
    const subusers = ServerContext.useStoreState((state) => state.subusers.data);
    const setSubusers = ServerContext.useStoreActions((actions) => actions.subusers.setSubusers);

    const permissions = useStoreState((state: ApplicationStore) => state.permissions.data);
    const getPermissions = useStoreActions((actions: Actions<ApplicationStore>) => actions.permissions.getPermissions);
    const { addError, clearFlashes } = useStoreActions((actions: Actions<ApplicationStore>) => actions.flashes);

    useEffect(() => {
        clearFlashes('users');
        getServerSubusers(uuid)
            .then((subusers) => {
                setSubusers(subusers);
                setLoading(false);
            })
            .catch((error) => {
                console.error(error);
                addError({ key: 'users', message: httpErrorToHuman(error) });
            });
    }, []);

    useEffect(() => {
        getPermissions().catch((error) => {
            addError({ key: 'users', message: httpErrorToHuman(error) });
            console.error(error);
        });
    }, []);

    if (!subusers.length && (loading || !Object.keys(permissions).length)) {
        return <Spinner size={'large'} centered />;
    }

    return (
        <ServerContentBlock
            title={'Users'}
            description={'Add and remove users from your server.'}
            showFlashKey={'users'}
        >
            {!subusers.length ? (
                <p css={tw`text-center text-sm text-neutral-300`}>It looks like you don&apos;t have any subusers.</p>
            ) : (
                subusers.map((subuser) => <UserRow key={subuser.uuid} subuser={subuser} />)
            )}
            <Can action={'user.create'}>
                <div css={tw`flex justify-end mt-6`}>
                    <AddSubuserButton />
                </div>
            </Can>
        </ServerContentBlock>
    );
};
